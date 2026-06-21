<?php

namespace App\Http\Controllers;

use App\enums\OrderChannel;
use App\enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    private const DEFAULT_PER_PAGE = 8;

    private const PER_PAGE_OPTIONS = [8, 10, 15, 25];

    public function index(Request $request): View|string
    {
        $orders = $this->buildOrderQuery($request)
            ->paginate($this->resolvePerPage($request))
            ->withQueryString();

        if ($request->ajax()) {
            return view('orders.partials.table', [
                'orders' => $orders,
            ])->render();
        }

        return view('orders.index', [
            'orderChannelOptions' => OrderChannel::options(),
            'orderMetricCards' => $this->orderMetricCards(),
            'orderStatusOptions' => OrderStatus::options(),
            'orders' => $orders,
        ]);
    }

    public function show(Order $order): View
    {
        $order->load([
            'items.product:id,name,sku,image,description,status',
        ]);

        return view('orders.show', $this->showViewData($order));
    }

    private function buildOrderQuery(Request $request): Builder
    {
        $search = $request->string('search')->trim()->value();
        $status = $request->enum('status', OrderStatus::class);
        $channel = $request->enum('channel', OrderChannel::class);
        $dateFrom = $request->string('date_from')->trim()->value();
        $dateTo = $request->string('date_to')->trim()->value();

        return Order::query()
            ->withCount('items')
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $nestedQuery) use ($search): void {
                    $nestedQuery
                        ->where('order_number', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%");
                });
            })
            ->when($status !== null, function (Builder $query) use ($status): void {
                $query->where('status', $status->value);
            })
            ->when($channel !== null, function (Builder $query) use ($channel): void {
                $query->where('channel', $channel->value);
            })
            ->when($dateFrom !== '', function (Builder $query) use ($dateFrom): void {
                $query->whereDate('ordered_at', '>=', $dateFrom);
            })
            ->when($dateTo !== '', function (Builder $query) use ($dateTo): void {
                $query->whereDate('ordered_at', '<=', $dateTo);
            })
            ->latest('ordered_at')
            ->latest('id');
    }

    /**
     * @return list<array{label: string, value: int, color: string}>
     */
    private function orderMetricCards(): array
    {
        $totalOrders = Order::query()->count();
        $pendingOrders = Order::query()->where('status', OrderStatus::Pending->value)->count();
        $fulfilledOrders = Order::query()->whereIn('status', OrderStatus::fulfilledValues())->count();
        $cancelledOrders = Order::query()->where('status', OrderStatus::Cancelled->value)->count();

        return [
            [
                'label' => 'Total Orders',
                'value' => $totalOrders,
                'color' => 'blue',
            ],
            [
                'label' => OrderStatus::Pending->label(),
                'value' => $pendingOrders,
                'color' => 'amber',
            ],
            [
                'label' => 'Fulfilled',
                'value' => $fulfilledOrders,
                'color' => 'emerald',
            ],
            [
                'label' => OrderStatus::Cancelled->label(),
                'value' => $cancelledOrders,
                'color' => 'rose',
            ],
        ];
    }

    /**
     * @return array{
     *     customerEmail: string,
     *     order: Order,
     *     orderChannelLabel: string,
     *     orderFinancialRows: list<array{label: string, value: string}>,
     *     orderInsights: array{status_note: string, top_item: string, average_unit_price: string},
     *     orderMetrics: list<array{label: string, value: string, helper: string, tone: string}>,
     *     orderStatusLabel: string,
     *     orderStatusValue: string
     * }
     */
    private function showViewData(Order $order): array
    {
        $status = $order->status;
        $itemsCount = $order->items->count();
        $unitsCount = (int) $order->items->sum('quantity');
        $itemsSubtotal = (float) $order->items->sum('line_total');
        $orderTotal = (float) $order->total;
        $difference = $orderTotal - $itemsSubtotal;
        $topItem = $order->items
            ->sortByDesc(fn ($item) => (float) $item->line_total)
            ->first();
        $averageUnitPrice = $unitsCount > 0
            ? $itemsSubtotal / $unitsCount
            : 0.0;

        $orderFinancialRows = [
            [
                'label' => 'Items Subtotal',
                'value' => '$'.number_format($itemsSubtotal, 2),
            ],
        ];

        if (abs($difference) > 0.009) {
            $orderFinancialRows[] = [
                'label' => $difference > 0 ? 'Adjustment' : 'Discount',
                'value' => ($difference > 0 ? '+' : '-').'$'.number_format(abs($difference), 2),
            ];
        }

        $orderFinancialRows[] = [
            'label' => 'Final Total',
            'value' => '$'.number_format($orderTotal, 2),
        ];

        $statusNote = match ($status) {
            OrderStatus::Paid, OrderStatus::Completed => 'This order is included in fulfilled revenue and product performance analytics.',
            OrderStatus::Pending => 'This order is waiting for completion and is not counted as fulfilled revenue yet.',
            OrderStatus::Cancelled => 'This order was cancelled and is excluded from fulfilled revenue.',
        };

        return [
            'customerEmail' => $order->customer_email ?: 'No email on file',
            'order' => $order,
            'orderChannelLabel' => $order->channel->label(),
            'orderFinancialRows' => $orderFinancialRows,
            'orderInsights' => [
                'status_note' => $statusNote,
                'top_item' => $topItem?->product_name ?? 'No items recorded',
                'average_unit_price' => '$'.number_format($averageUnitPrice, 2),
            ],
            'orderMetrics' => [
                [
                    'label' => 'Order Value',
                    'value' => '$'.number_format($orderTotal, 2),
                    'helper' => 'Current order total',
                    'tone' => 'blue',
                ],
                [
                    'label' => 'Items',
                    'value' => number_format($itemsCount),
                    'helper' => 'Unique line items',
                    'tone' => 'slate',
                ],
                [
                    'label' => 'Units',
                    'value' => number_format($unitsCount),
                    'helper' => 'Total quantity sold',
                    'tone' => 'emerald',
                ],
                [
                    'label' => 'Placed',
                    'value' => $order->ordered_at?->format('M d, Y') ?? $order->created_at->format('M d, Y'),
                    'helper' => $order->ordered_at?->format('h:i A') ?? 'Recorded date',
                    'tone' => 'violet',
                ],
            ],
            'orderStatusLabel' => $status->label(),
            'orderStatusValue' => $status->value,
        ];
    }

    private function resolvePerPage(Request $request): int
    {
        $perPage = $request->integer('per_page', self::DEFAULT_PER_PAGE);

        return in_array($perPage, self::PER_PAGE_OPTIONS, true)
            ? $perPage
            : self::DEFAULT_PER_PAGE;
    }
}
