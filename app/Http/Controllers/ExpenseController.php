<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseStoreRequest;
use App\Http\Requests\ExpenseUpdateRequest;
use App\Models\Expense;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with('trip')
            ->orderBy('expense_date', 'desc');

        // Filter by trip if provided
        if ($request->filled('trip_id')) {
            $query->where('trip_id', $request->trip_id);
        }

        // Filter by year
        if ($request->filled('year')) {
            $query->where('fiscal_year', $request->year);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $expenses = $query->paginate(20)->withQueryString();
        $trips = Trip::orderBy('departure_date', 'desc')->get(['id', 'name']);
        $years = Expense::selectRaw('DISTINCT fiscal_year')->orderBy('fiscal_year', 'desc')->pluck('fiscal_year');
        $categories = ['hotel', 'transport', 'food', 'visa', 'flights', 'other'];

        return Inertia::render('Finance/Expenses/Index', [
            'expenses' => $expenses,
            'trips' => $trips,
            'years' => $years,
            'categories' => $categories,
            'filters' => $request->only(['trip_id', 'year', 'category']),
        ]);
    }

    public function create(Request $request)
    {
        $trips = Trip::orderBy('departure_date', 'desc')->get(['id', 'name']);
        $categories = ['hotel', 'transport', 'food', 'visa', 'flights', 'other'];

        return Inertia::render('Finance/Expenses/Create', [
            'trips' => $trips,
            'categories' => $categories,
            'preselectedTripId' => $request->query('trip_id'),
        ]);
    }

    public function store(ExpenseStoreRequest $request)
    {
        $data = $request->validated();

        // Handle document upload
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('expenses')) {
                Storage::disk('public')->makeDirectory('expenses');
            }

            $path = $file->storeAs('expenses', $fileName, 'public');

            $data['document_path'] = $path;
            $data['document_filename'] = $file->getClientOriginalName();
            $data['document_mime_type'] = $file->getClientMimeType();
        }

        // Set fiscal year based on expense date if not provided
        if (empty($data['fiscal_year'])) {
            $data['fiscal_year'] = date('Y', strtotime($data['expense_date']));
        }

        Expense::create($data);

        return redirect()
            ->route('finance.expenses.index')
            ->with('success', 'Expense added successfully');
    }

    public function show(Expense $expense)
    {
        $expense->load('trip');

        return Inertia::render('Finance/Expenses/Show', [
            'expense' => $expense,
        ]);
    }

    public function edit(Expense $expense)
    {
        $trips = Trip::orderBy('departure_date', 'desc')->get(['id', 'name']);
        $categories = ['hotel', 'transport', 'food', 'visa', 'flights', 'other'];

        return Inertia::render('Finance/Expenses/Edit', [
            'expense' => $expense,
            'trips' => $trips,
            'categories' => $categories,
        ]);
    }

    public function update(ExpenseUpdateRequest $request, Expense $expense)
    {
        $data = $request->validated();

        // Handle document upload/replacement
        if ($request->hasFile('document')) {
            // Delete old document
            if ($expense->document_path && Storage::disk('public')->exists($expense->document_path)) {
                Storage::disk('public')->delete($expense->document_path);
            }

            $file = $request->file('document');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            if (!Storage::disk('public')->exists('expenses')) {
                Storage::disk('public')->makeDirectory('expenses');
            }

            $path = $file->storeAs('expenses', $fileName, 'public');

            $data['document_path'] = $path;
            $data['document_filename'] = $file->getClientOriginalName();
            $data['document_mime_type'] = $file->getClientMimeType();
        }

        $expense->update($data);

        return redirect()
            ->route('finance.expenses.index')
            ->with('success', 'Expense updated successfully');
    }

    public function destroy(Expense $expense)
    {
        // Delete document if exists
        if ($expense->document_path && Storage::disk('public')->exists($expense->document_path)) {
            Storage::disk('public')->delete($expense->document_path);
        }

        $expense->delete();

        return redirect()
            ->route('finance.expenses.index')
            ->with('success', 'Expense deleted successfully');
    }

    public function deleteDocument(Expense $expense)
    {
        if ($expense->document_path && Storage::disk('public')->exists($expense->document_path)) {
            Storage::disk('public')->delete($expense->document_path);
        }

        $expense->update([
            'document_path' => null,
            'document_filename' => null,
            'document_mime_type' => null,
        ]);

        return redirect()->back()->with('success', 'Document deleted successfully');
    }
}
