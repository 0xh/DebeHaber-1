<?php

namespace App\Http\Controllers;

use App\Http\Resources\GeneralResource;
use App\TransactionDetail;
use DB;

class KPIController extends Controller
{
    public function transactionByItems($taxPayerID, $type, $startDate, $endDate)
    {
        return GeneralResource::collection(
            TransactionDetail::join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
                ->join('charts', 'charts.id', '=', 'transaction_details.chart_id')
                ->where('transactions.taxpayer_id', $taxPayerID)
                ->where('transactions.type', $type)
                ->whereBetween('transactions.date', [$startDate, $endDate])
                ->select(
                    DB::raw('SUM(transactions.rate * transaction_details.value) as Value'),
                    'charts.name as Item'
                )
                ->groupBy('transaction_details.chart_id')
                ->get()
        );
    }

    public function transactionByDate($taxPayerID, $type, $startDate, $endDate)
    {
        return App\TransactionDetail::join('transactions', 'transactions.id', '=', 'transaction_details.transaction_id')
            ->join('charts', 'charts.id', '=', 'transaction_details.chart_id')
            ->where('transactions.taxpayer_id', $taxPayerID)
            ->where('transactions.type', $type)
            ->whereBetween('transactions.date', [$startDate, $endDate])
            ->select(
                DB::raw('(transactions.rate * transaction_details.value) as Value'),
                'charts.name as Item',
                'transactions.date as Date'
            )
            ->groupBy(DB::raw('Date(transactions.date)'))
            ->get();
    }
    public function totalVatDebit($taxPayerID)
    {
        return App\AccountMovement::join('charts', 'charts.id', '=', 'account_movements')
            ->where('charts.type', 1)
            ->Orwhere('charts.type', 12)
            ->select(
                DB::raw('sum(account_movements.debit) as Total'),
                'charts.name as Item'
            )
            ->groupBy('account_movements.chart_id')
            ->get();
    }

    public function totalVatCredit($taxPayerID)
    {
        return App\AccountMovement::join('charts', 'charts.id', '=', 'account_movements')
            ->where('charts.type', 2)
            ->Orwhere('charts.type', 3)
            ->select(
                DB::raw('sum(account_movements.credit) as Total'),
                'charts.name as Item'
            )
            ->groupBy('account_movements.chart_id')
            ->get();
    }

    public function DiffrenceinVat($taxPayerID)
    { }
}
