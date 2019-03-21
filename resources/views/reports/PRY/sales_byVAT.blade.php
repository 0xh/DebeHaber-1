

@extends('reports.master')
@section('reportName', __('commercial.SalesByVAT'))

@section('data')
    <table class="u-full-width">
        <tbody>
            @foreach ($data->groupBy('coefficient') as $groupedRows)
                <thead>
                    <tr>
                        <th> @lang('global.Date') </th>
                        <th> @lang('global.Taxid') </th>
                        <th> @lang('global.Taxpayer') </th>
                        <th class="number">{{ Config::get('countries.' . request()->route('taxPayer')->country . '.document-code') }}</th>
                        <th> @lang('commercial.Invoice') </th>
                        <th> @lang('commercial.Condition') </th>
                        <th class="number"> @lang('commercial.Taxable') 10% </th>
                        <th class="number"> @lang('commercial.SalesTax') 10% </th>
                        <th class="number"> @lang('commercial.Taxable') 5% </th>
                        <th class="number"> @lang('commercial.SalesTax') 5% </th>
                        <th class="number"> @lang('commercial.Exempt') </th>
                        <th class="number"> @lang('global.Total') </th>
                    </tr>
                </thead>
                <tr class="group">
                    <td colspan="3">
                        <h6 class="title is-6">{{ $groupedRows->first()->coefficient * 100 }} %</h6>
                    </td>
                    <td></td>
                    <td colspan="2"><h6 class="title is-6">@lang('global.SubTotal')</h6></td>
                    <td class="number"><h6 class="title is-6">{{ number_format($groupedRows->where('status', '!=', 3)->where('coefficient', '=', 0.1)->sum('vatValue'), 0, ',', '.') }}</h6></td>
                    <td class="number">
                        <h6 class="title is-6">
                            {{ number_format(($groupedRows->where('status', '!=', 3)->where('coefficient', '=', 0.1)->sum('localCurrencyValue') - $groupedRows->where('status', '!=', 3)->where('coefficient', '=', 0.1)->sum('vatValue')), 0, ',', '.') }}
                        </h6>
                    </td>
                    <td class="number"><h6 class="title is-6">{{ number_format($groupedRows->where('status', '!=', 3)->where('coefficient', '=', 0.05)->sum('vatValue'), 0, ',', '.') }}</h6></td>
                    <td class="number">
                        <h6 class="title is-6">
                            {{ number_format(($groupedRows->where('status', '!=', 3)->where('coefficient', '=', 0.05)->sum('localCurrencyValue') - $groupedRows->where('status', '!=', 3)->where('coefficient', '=', 0.05)->sum('vatValue')), 0, ',', '.') }}
                        </h6>
                    </td>
                    <td class="number"><h6 class="title is-6">{{ number_format($groupedRows->where('status', '!=', 3)->where('coefficient', '=', 0.00)->sum('vatValue'), 0, ',', '.') }}</h6></td>
                    <td class="number"><h6 class="title is-6">{{ number_format($groupedRows->where('status', '!=', 3)->sum('localCurrencyValue'), 0, ',', '.') }}</h6></td>

                    {{-- <td class="number"><h6 class="title is-6">{{ number_format($groupedRows->where('status', '!=', 3)->sum('vatValue'), 0, ',', '.') }}</h6></td> --}}
                </tr>
                @foreach ($groupedRows->groupBy('salesID') as $row)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($row->first()->date)->format('d/m/Y')}}</td>

                        <td class="important">{{ $row->first()->customer_code }}</td>

                        <td class="text">{{ $row->first()->customer }}</td>

                        <td class="number">{{ $row->first()->code }}</td>

                        <td class="important">
                            <a href="/{{ request()->route('taxPayer')->id }}/{{ request()->route('cycle')->id}}/commercial/sales/{{ $row->first()->salesID }}" target="_blank">
                                {{ $row->first()->number }}
                            </a>
                        </td>

                        <td>{{ $row->first()->payment_condition > 0 ? __('commercial.Credit') : __('commercial.Cash') }}</td>

                        <td class="number important">
                            {{ number_format($row->where('coefficient', '=', 0.1)->sum('vatValue'), 0, ',', '.') }}
                        </td>

                        <td class="number important">
                            {{ number_format($row->where('coefficient', '=', 0.1)->sum('localCurrencyValue') - $row->where('coefficient', '=', 0.1)->sum('vatValue'), 0, ',', '.') }}
                        </td>

                        <td class="number important">
                            {{ number_format($row->where('coefficient', '=', 0.05)->sum('vatValue'), 0, ',', '.') }}
                        </td>

                        <td class="number important">
                            {{ number_format($row->where('coefficient', '=', 0.05)->sum('localCurrencyValue') - $row->where('coefficient', '=', 0.05)->sum('vatValue'), 0, ',', '.') }}
                        </td>

                        <td class="number important">
                            {{ number_format($row->where('coefficient', '=', 0)->sum('localCurrencyValue'), 0, ',', '.') }}
                        </td>

                        <td class="number important">{{ number_format($row->sum('localCurrencyValue'), 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            @endforeach
            <tr class="group">
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td colspan="2"><h6 class="title is-6">@lang('global.GrandTotal')</h6></td>
                <td class="number"><h6 class="title is-6">{{ number_format($data->where('status', '!=', 3)->where('coefficient', '=', 0.1)->sum('vatValue'), 0, ',', '.') }}</h6></td>
                <td class="number"><h6 class="title is-6">{{ number_format(($data->where('status', '!=', 3)->where('coefficient', '=', 0.1)->sum('localCurrencyValue') - $data->where('status', '!=', 3)->where('coefficient', '=', 0.1)->sum('vatValue')), 0, ',', '.') }}</h6></td>
                <td class="number"><h6 class="title is-6">{{ number_format($data->where('status', '!=', 3)->where('coefficient', '=', 0.05)->sum('vatValue'), 0, ',', '.') }}</h6></td>
                <td class="number"><h6 class="title is-6">{{ number_format(($data->where('status', '!=', 3)->where('coefficient', '=', 0.05)->sum('localCurrencyValue') - $data->where('status', '!=', 3)->where('coefficient', '=', 0.05)->sum('vatValue')), 0, ',', '.') }}</h6></td>
                <td class="number"><h6 class="title is-6">{{ number_format($data->where('status', '!=', 3)->where('coefficient', '=', 0.00)->sum('vatValue'), 0, ',', '.') }}</h6></td>
                <td class="number"><h6 class="title is-6">{{ number_format($data->where('status', '!=', 3)->sum('localCurrencyValue'), 0, ',', '.') }}</h6></td>
            </tr>
        </tbody>
    </table>
@endsection
