<?php

namespace App\Http\Controllers;

use App\Models\CustomerActivity;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Trip;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FinanceController extends Controller
{
    public function dashboard(Request $request)
    {
        $selectedYear = $request->query('year', date('Y'));

        // Get all years with financial data
        $years = $this->getAvailableYears();

        // Trip-based financial summaries
        $tripFinancials = $this->getTripFinancials($selectedYear);

        // Yearly summary
        $yearlySummary = $this->getYearlySummary($selectedYear);

        // Monthly breakdown for charts
        $monthlyData = $this->getMonthlyData($selectedYear);

        // Expenses by category
        $expensesByCategory = $this->getExpensesByCategory($selectedYear);

        return Inertia::render('Finance/Dashboard', [
            'selectedYear' => (int) $selectedYear,
            'years' => $years,
            'tripFinancials' => $tripFinancials,
            'yearlySummary' => $yearlySummary,
            'monthlyData' => $monthlyData,
            'expensesByCategory' => $expensesByCategory,
        ]);
    }

    public function tripReport(Trip $trip)
    {
        // Get all payments for this trip
        $invoiceIds = Invoice::where('trip_id', $trip->id)->pluck('id');
        $payments = Payment::whereIn('invoice_id', $invoiceIds)
            ->with('invoice.customer')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get activity payments
        $activityPayments = CustomerActivity::whereHas('activityTrip', function ($q) use ($trip) {
            $q->where('trip_id', $trip->id);
        })
            ->whereNotNull('paid_at')
            ->with(['customer', 'activityTrip.activity'])
            ->orderBy('paid_at', 'desc')
            ->get();

        // Get expenses for this trip
        $expenses = Expense::where('trip_id', $trip->id)
            ->orderBy('expense_date', 'desc')
            ->get();

        // Calculate totals
        $totalRevenue = $payments->sum('amount') + $activityPayments->sum('amount_paid');
        $totalExpenses = $expenses->sum('amount');
        $netProfit = $totalRevenue - $totalExpenses;

        return Inertia::render('Finance/TripReport', [
            'trip' => $trip,
            'payments' => $payments,
            'activityPayments' => $activityPayments,
            'expenses' => $expenses,
            'summary' => [
                'totalRevenue' => $totalRevenue,
                'totalExpenses' => $totalExpenses,
                'netProfit' => $netProfit,
                'customerCount' => $trip->customers()->count(),
            ],
        ]);
    }

    private function getAvailableYears(): array
    {
        $expenseYears = Expense::selectRaw('DISTINCT fiscal_year as year')
            ->whereNotNull('fiscal_year')
            ->pluck('year')
            ->toArray();

        $tripYears = Trip::selectRaw('DISTINCT YEAR(departure_date) as year')
            ->whereNotNull('departure_date')
            ->pluck('year')
            ->toArray();

        $years = array_unique(array_merge($expenseYears, $tripYears, [(int) date('Y')]));
        rsort($years);

        return array_values(array_filter($years));
    }

    private function getTripFinancials($year): array
    {
        return Trip::whereYear('departure_date', $year)
            ->withCount('customers')
            ->orderBy('departure_date', 'desc')
            ->get()
            ->map(function ($trip) {
                $invoiceIds = Invoice::where('trip_id', $trip->id)->pluck('id');
                $revenue = Payment::whereIn('invoice_id', $invoiceIds)->sum('amount');

                // Add activity revenue
                $activityRevenue = CustomerActivity::whereHas('activityTrip', function ($q) use ($trip) {
                    $q->where('trip_id', $trip->id);
                })->whereNotNull('paid_at')->sum('amount_paid');

                $expenses = Expense::where('trip_id', $trip->id)->sum('amount');

                return [
                    'id' => $trip->id,
                    'name' => $trip->name,
                    'departure_date' => $trip->departure_date,
                    'customers_count' => $trip->customers_count,
                    'revenue' => (float) ($revenue + $activityRevenue),
                    'expenses' => (float) $expenses,
                    'profit' => (float) (($revenue + $activityRevenue) - $expenses),
                ];
            })
            ->toArray();
    }

    private function getYearlySummary($year): array
    {
        // Revenue from invoices (trips in this year)
        $tripIds = Trip::whereYear('departure_date', $year)->pluck('id');
        $invoiceIds = Invoice::whereIn('trip_id', $tripIds)->pluck('id');
        $invoiceRevenue = Payment::whereIn('invoice_id', $invoiceIds)->sum('amount');

        // Activity revenue
        $activityRevenue = CustomerActivity::whereHas('activityTrip', function ($q) use ($tripIds) {
            $q->whereIn('trip_id', $tripIds);
        })->whereNotNull('paid_at')->sum('amount_paid');

        // Expenses
        $tripExpenses = Expense::whereIn('trip_id', $tripIds)->sum('amount');
        $generalExpenses = Expense::whereNull('trip_id')->where('fiscal_year', $year)->sum('amount');

        $totalRevenue = $invoiceRevenue + $activityRevenue;
        $totalExpenses = $tripExpenses + $generalExpenses;

        return [
            'totalRevenue' => (float) $totalRevenue,
            'invoiceRevenue' => (float) $invoiceRevenue,
            'activityRevenue' => (float) $activityRevenue,
            'totalExpenses' => (float) $totalExpenses,
            'tripExpenses' => (float) $tripExpenses,
            'generalExpenses' => (float) $generalExpenses,
            'netProfit' => (float) ($totalRevenue - $totalExpenses),
            'tripCount' => $tripIds->count(),
        ];
    }

    private function getMonthlyData($year): array
    {
        $months = [];

        // Get trip IDs for trips in this year
        $tripIds = Trip::whereYear('departure_date', $year)->pluck('id');
        $invoiceIds = Invoice::whereIn('trip_id', $tripIds)->pluck('id');

        for ($month = 1; $month <= 12; $month++) {
            // Revenue from payments made this month
            $revenue = Payment::whereIn('invoice_id', $invoiceIds)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->sum('amount');

            // Activity revenue this month
            $activityRevenue = CustomerActivity::whereHas('activityTrip', function ($q) use ($tripIds) {
                $q->whereIn('trip_id', $tripIds);
            })
                ->whereYear('paid_at', $year)
                ->whereMonth('paid_at', $month)
                ->sum('amount_paid');

            // Expenses this month (trip expenses + general expenses)
            $expenses = Expense::where(function ($q) use ($tripIds, $year) {
                $q->whereIn('trip_id', $tripIds)
                    ->orWhere(function ($q2) use ($year) {
                        $q2->whereNull('trip_id')->where('fiscal_year', $year);
                    });
            })
                ->whereMonth('expense_date', $month)
                ->whereYear('expense_date', $year)
                ->sum('amount');

            $months[] = [
                'month' => $month,
                'monthName' => date('M', mktime(0, 0, 0, $month, 1)),
                'revenue' => (float) ($revenue + $activityRevenue),
                'expenses' => (float) $expenses,
                'profit' => (float) (($revenue + $activityRevenue) - $expenses),
            ];
        }

        return $months;
    }

    private function getExpensesByCategory($year): array
    {
        return Expense::where('fiscal_year', $year)
            ->selectRaw('COALESCE(category, "other") as category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category ?? 'other',
                    'total' => (float) $item->total,
                ];
            })
            ->toArray();
    }
}
