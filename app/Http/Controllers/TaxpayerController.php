<?php

namespace App\Http\Controllers;

use App\Transaction;
use App\Taxpayer;
use App\TaxpayerIntegration;
use App\TaxpayerType;
use App\ChartVersion;
use App\Cycle;
use App\Chart;
use App\Team;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TaxpayerController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {

    }

    public function get_taxpayer($country, $queryString)
    {
        //this function allows fuzzy search and add importance to certain fields.
        $taxPayers = Taxpayer::search($queryString,
        function (\Elasticsearch\Client $client, $query, $params) {
            $params['body']['query'] = [
                'multi_match' => [
                    'query' => $query,
                    'fuzziness' => 'AUTO',
                    'fields' => ['taxid', 'alias^3', 'name'],
                ],
            ];
            return $client->search($params);
        })
        ->take(25)
        ->get();

        return response()->json($taxPayers);
    }

    public function get_owner($country, $id)
    {
        $taxPayer_Integration = TaxpayerIntegration::where('taxpayer_id', $id)
        ->where('is_owner', '1')
        ->with('team')
        ->first();

        if (isset($taxPayer_Integration))
        {
            return response()->json($taxPayer_Integration, 200);
        }

        return response()->json(null, 204);
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
        
        //Used below for date and year.
        $current_date = Carbon::now();

        //TODO Request ID must be of Integration, not Taxpayer. From there you can know if taxpayer exists.

        //Check Taxpayer by TaxID. If exists, use it, or else create it.
        $taxPayer = Taxpayer::where('taxid', $request->taxid)
        ->where('country', Auth::user()->country)
        ->first();

        //If taxpayer does not exist, then create it.
        if (!isset($taxPayer))
        {
            $taxPayer= new Taxpayer();
            $taxPayer->name = $request->name;
            $taxPayer->taxid = $request->taxid > 0 ? $request->taxid : 0;
            $taxPayer->code = $taxPayer->code ?? $request->code;

            $taxPayer->country = Auth::user()->country;
        }

        //Update basic information
        $taxPayer->alias = $taxPayer->alias ?? $request->alias;
        $taxPayer->address = $taxPayer->address ?? $request->address;
        $taxPayer->telephone = $taxPayer->telephone ?? $request->telephone;
        $taxPayer->email = $taxPayer->email ?? $request->email;

        $taxPayer->agent_name = $request->setting_agent = true ? 1 : 0;
        $taxPayer->agent_taxid = $request->setting_agenttaxid = true ? 1 : 0;

        $taxPayer->show_inventory = $request->setting_inventory = true ? 1 : 0;
        $taxPayer->show_production = $request->setting_production = true ? 1 : 0;
        $taxPayer->show_fixedasset = $request->setting_fixedasset = true ? 1 : 0;

        // $taxPayer->does_export = $request->setting_export = true ? 1 : 0;
        // $taxPayer->does_import = $request->setting_import = true ? 1 : 0;

        $taxPayer->is_company = $request->setting_is_company = true ? 1 : 0 ;
        $taxPayer->save();

        $chartVersion = ChartVersion::where('country', $taxPayer->country)
        ->where(function($query) use($taxPayer)
        {
            $query
            ->where('taxpayer_id', $taxPayer->id)
            ->orWhere('taxpayer_id', null);
        })
        ->first();

        if (!isset($chartVersion))
        {
            $chartVersion = new ChartVersion();
            $chartVersion->name = $current_date->year;
            $chartVersion->taxpayer_id = $taxPayer->id;
            $chartVersion->country = $taxPayer->country;
            $chartVersion->save();
        }

        $cycle = Cycle::where('chart_version_id', $chartVersion->id)
        ->where('taxpayer_id', $taxPayer->id)
        ->first();

        if (!isset($cycle))
        { $cycle = new Cycle(); }

        $cycle->chart_version_id = $chartVersion->id;
        $cycle->year = $current_date->year;
        $cycle->start_date = new Carbon('first day of January');
        $cycle->end_date = new Carbon('last day of December');
        $cycle->taxpayer_id = $taxPayer->id;
        $cycle->save();

        //Search for owner of the integration with this taxpayer.
        $taxPayer_Integration = TaxpayerIntegration::where('taxpayer_id', $taxPayer->id)
        ->where('is_owner', 1)
        ->first();
       
        if  (!isset($taxPayer_Integration))
        {
                //Even though integration exists, you need to create a new integration that is specific for this team.
                 $taxPayer_Integration = new TaxpayerIntegration();
                 $taxPayer_Integration->is_owner =  1;
                 $taxPayer_Integration->status =  2;
        }
        else
        {
                 $taxPayer_Integration->is_owner =  0;
                 $taxPayer_Integration->status =  1;
        }
       
       
        $taxPayer_Integration->taxpayer_id = $taxPayer->id;
        $taxPayer_Integration->team_id = Auth::user()->current_team_id;
        $taxPayer_Integration->type = $request->type ?? 1; //Default to 1 if nothing is selected
        $taxPayer_Integration->notification_monthly = $request->notification_monthly == true ? 1 : 0;
        $taxPayer_Integration->notification_quarterly = $request->notification_quarterly == true ? 1 : 0;
        $taxPayer_Integration->notification_semesterly = $request->notification_semesterly == true ? 1 : 0;
        $taxPayer_Integration->notification_yearly = $request->notification_yearly == true ? 1 : 0;
        $taxPayer_Integration->notification_sync = $request->notification_sync == true ? 1 : 0;
        $taxPayer_Integration->save();

        $team = Team::find(Auth::user()->current_team_id);
        $team->addSeat();

        //Send an email to user or team members.
        return view('taxpayer')->with('taxPayer', $taxPayer);
    }

    //This is for Customers or Suppliers that are also Taxpayers.
    public function createTaxPayer(Request $request, Taxpayer $taxPayer)
    {
        $customerOrSupplier = Taxpayer::where('taxid', $request->taxid)->where('country', $taxPayer->country)->first();

        if (!isset($customerOrSupplier))
        {
            $customerOrSupplier= new Taxpayer();
            $customerOrSupplier->name = $request->name;
        }

        //TODO Country from Selection Box
        $customerOrSupplier->taxid = $request->taxid;
        $customerOrSupplier->code = $request->code;

        $customerOrSupplier->alias = $request->alias;
        $customerOrSupplier->address = $request->address;
        $customerOrSupplier->telephone = $request->telephone;
        $customerOrSupplier->email = $request->email;

        $customerOrSupplier->save();

        return response()->json('ok', 200);
    }

    /**
    * Display the specified resource.
    *
    * @param  \App\Taxpayer  $taxpayer
    * @return \Illuminate\Http\Response
    */
    public function show($taxPayer)
    {
        $taxPayer = Taxpayer::where('id', $taxPayer)
        ->with('integrations')
        ->with('currencies')
        ->first();
        
        return view('taxpayer')->with('taxPayer', $taxPayer);
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\Taxpayer  $taxpayer
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, Taxpayer $taxPayer)
    {
      
        if (isset($taxPayer))
        {
            $taxPayer->alias = $request->alias;
            $taxPayer->address = $request->address;
            $taxPayer->telephone = $request->telephone;
            $taxPayer->email = $request->email;

            $taxPayer->taxpayer_id = $taxPayer->id;
            $taxPayer->agent_taxid = $request->agent_taxid;
            $taxPayer->agent_name = $request->agent_name;

              
             $taxPayer->integration[0]->notification_monthly = $request->notification_monthly == true ? 1 : 0;
            $taxPayer->integration[0]->notification_quarterly = $request->notification_quarterly == true ? 1 : 0;
            $taxPayer->integration[0]->notification_semesterly = $request->notification_semesterly == true ? 1 : 0;
            $taxPayer->integration[0]->notification_yearly = $request->notification_yearly == true ? 1 : 0;
            $taxPayer->integration[0]->notification_sync = $request->notification_sync == true ? 1 : 0;

            $taxPayer->save();

            $taxpayertypes = TaxpayerType::where('taxpayer_id', $taxPayer->id)->get();

            if (isset($taxpayertypes)) {
                foreach ($taxpayertypes as $taxpayertype)
                {
                    $taxpayertype->delete();
                }
            }

            if (isset($request->type)) {
                foreach ($request->type as  $value) {
                    $taxpayertype = new TaxpayerType();
                    $taxpayertype->taxpayer_id = $taxPayer->id;
                    $taxpayertype->type = $value;
                    $taxpayertype->save();
                }
            }

          return view('taxpayer')->with('taxPayer', $taxPayer);
        }

         return view('taxpayer')->with('taxPayer', $taxPayer);
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Taxpayer  $taxpayer
    * @return \Illuminate\Http\Response
    */
    public function destroy(Taxpayer $taxPayer)
    {
        //
    }

    public function showDashboard($taxPayer, $cycle)
    {
        // $chartsAccountable = 1; //Chart::where('is_accountable', true)->pluck('type', 'sub_type')->get();
        // $chartFixedAssets = 1; //$chartsAccountable->where('type', 1)->where('sub_type', 9)->count();
        // $chartMoneyAccounts = 1; //$chartsAccountable->where('type', 1)->where('sub_type', 1)->count();
        // $chartExpenses = 1; //$chartsAccountable->where('type', 5)->count();
        // $chartInventories = 1; //$chartsAccountable->where('type', 1)->where('sub_type', 8)->count();
        // $chartIncomes = 1; //$chartsAccountable->where('type', 4)->count();
        //
        // $totalSales = Transaction::MySales()
        // ->whereBetween('date', [new Carbon('first day of last month'), new Carbon('last day of last month')])
        // ->where('supplier_id', $taxPayer->id)
        // ->count();
        //
        // $totalPurchases = Transaction::MyPurchases()
        // ->whereBetween('date', [new Carbon('first day of last month'), new Carbon('last day of last month')])
        // ->where('customer_id', $taxPayer->id)
        // ->count();

        return view('platform');
    }

    public function selectTaxpayer(Request $request, Taxpayer $taxPayer)
    {
        //Get current month sub 1 month.
        $workingYear = Carbon::now()->subMonth(1)->year;

        //Check if there is Cycle of current year.
        $cycle = Cycle::where('year', $workingYear)
        ->where('taxpayer_id', $taxPayer->id)
        ->first();

        //If null, then create it.
        if (isset($cycle) == false)
        {
            //TODO Get Last ChartVersion or Default..
            $chartVersion = ChartVersion::where('country', $taxPayer->country)->first() ?? new ChartVersion();

            if ($chartVersion->id != null || $chartVersion->id == 0)
            {
                $chartVersion->country = $taxPayer->country;
                $chartVersion->name = "Generic";
                $chartVersion->save();
            }

            $cycle = new Cycle();
            $cycle->chart_version_id = $chartVersion->id;
            $cycle->year = $workingYear;
            $cycle->start_date = new Carbon('first day of January ' . $workingYear);
            $cycle->end_date = new Carbon('last day of December ' . $workingYear);
            $cycle->taxpayer_id = $taxPayer->id;
            $cycle->save();
        }

        //run code to check for fiscal year selection and create if not.
        return redirect()->route('taxpayer.dashboard', [$taxPayer, $cycle]);
    }
}
