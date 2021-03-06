@auth
    @php
    $currentTeam = Auth::user()->currentTeam;

    if (isset($currentTeam) && request()->route('taxPayer') != null) {

        $taxPayerData = App\Taxpayer::where('id', request()->route('taxPayer'))
        ->select('id', 'name', 'alias', 'taxid', 'country', 'currency')
        ->first();
     
        $taxPayerConfig = Config::get('countries.' . $taxPayerData->country);

        $cycleData = App\Cycle::where('taxpayer_id', request()->route('taxPayer'))
        ->select('id', 'year')
        ->orderBy('year', 'desc')
        ->take(3)
        ->get();

        $currentCycle = App\Cycle::where('id', request()->route('cycle'))->first();

        $integrationType = App\TaxpayerIntegration::where('team_id', $currentTeam->id)
        ->where('taxpayer_id', request()->route('taxPayer'))
        ->whereIn('status', [1, 2])
        ->select('id','type')
        ->first();

        if (isset($integrationType))
        {
            if ($integrationType->type == 2) {
                $teamRole = 'Individual';
            } else if ($integrationType->type == 3) {
                $teamRole = 'Audit';
            } else {
                $teamRole = 'Accounting';
            }
        }
    }
    @endphp
@endauth

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta Information -->
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        @if (isset($taxPayerData) && isset($currentCycle))
            DH | {{ $taxPayerData->alias }} @ {{ $currentCycle->year }}
        @else
            @yield('title', config('app.name'))
        @endif
    </title>

    <!-- Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Raleway:300,400,600' rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    {{-- Remove This --}}
    {{-- <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css' rel='stylesheet' type='text/css'> --}}
    <!-- CSS -->
    <link href="{{ mix(Spark::usesRightToLeftTheme() ? 'css/app-rtl.css' : 'css/app.css') }}" rel="stylesheet">

    <!-- Scripts -->
    @stack('scripts')

    <!-- Global Spark Object -->
    <script>
    window.Spark = @json(array_merge(Spark::scriptVariables(), []));
    window.Spark = <?php echo json_encode(array_merge(
        Spark::scriptVariables(), [
            'taxPayerData' => $taxPayerData ?? [],
            'taxPayerConfig' => $taxPayerConfig ?? [],
            'teamRole' => $teamRole ?? '',

            'language' => Auth::user() != null ? Auth::user()->language : 'en',

            'enumChartType' => App\Enums\ChartTypeEnum::labels(),
            'enumAsset' => App\Enums\ChartAssetTypeEnum::labels(),
            'enumEquity' => App\Enums\ChartEquityTypeEnum::labels(),
            'enumExpense' => App\Enums\ChartExpenseTypeEnum::labels(),
            'enumLiability' => App\Enums\ChartLiabilityTypeEnum::labels(),
            'enumRevenue' => App\Enums\ChartRevenueTypeEnum::labels(),
        ]
    )); ?>
    </script>
</head>

<body>
    <div id="spark-app" v-cloak>
        <!-- Navigation -->
        @if (Auth::check())
            @include('spark::nav.user')
        @else
            @include('spark::nav.guest')
        @endif

        <!-- Main Content -->
        <b-container class="spark-screen" fluid>
            @if (request()->route('taxPayer') != null)
                <b-row>
                    <!-- App Menu -->
                    <b-collapse id="menu" visible class="col-lg-2 spark-settings-tabs">
                        @include('spark::nav.apps')
                    </b-collapse>
                    <!-- Main Content -->
                    <b-col>
                        @yield('content')
                    </b-col>
                </b-row>
            @else
                @yield('content')
            @endif
        </b-container>
        <!-- Application Level Modals -->
        @if (Auth::check())
            @include('spark::modals.notifications')
            @include('spark::modals.support')
            @include('spark::modals.session-expired')
        @endif
    </div>

    <!-- JavaScript -->
    <script src="{{ mix('js/app.js') }}"></script>
    {{-- <script src="/js/sweetalert.min.js"></script> --}}
</body>
</html>
