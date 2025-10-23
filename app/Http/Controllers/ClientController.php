<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Setting;
use App\utils\helpers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\SaleReturn;
use App\Models\PaymentSaleReturns;
use App\Models\Sale;
use App\Models\Role;
use App\Models\User;
use App\Models\PaymentSale;
use DB;
use Illuminate\Http\Request;

class ClientController extends BaseController
{

    //------------- Get ALL Customers -------------\\

    public function index(request $request)
    {
        $this->authorizeForUser($request->user('api'), 'view', Client::class);
        // How many items do you want to display.
        $perPage = $request->limit;
        $pageStart = \Request::get('page', 1);
        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;
        $order = $request->SortField;
        $dir = $request->SortType;
        $helpers = new helpers();
        // Filter fields With Params to retrieve
        $columns = array(0 => 'name', 1 => 'code', 2 => 'phone', 3 => 'email');
        $param = array(0 => 'like', 1 => 'like', 2 => 'like', 3 => 'like');
        $data = array();
        $clients = Client::with("user")->where('deleted_at', '=', null);

        //Multiple Filter
        $Filtred = $helpers->filter($clients, $columns, $param, $request)
        // Search With Multiple Param
            ->where(function ($query) use ($request) {
                return $query->when($request->filled('search'), function ($query) use ($request) {
                    return $query->where('name', 'LIKE', "%{$request->search}%")
                        ->orWhere('code', 'LIKE', "%{$request->search}%")
                        ->orWhere('phone', 'LIKE', "%{$request->search}%")
                        ->orWhere('email', 'LIKE', "%{$request->search}%");
                });
            });
        $totalRows = $Filtred->count();
        if($perPage == "-1")
            $perPage = $totalRows;

        $clients = $Filtred->offset($offSet)
            ->limit($perPage)
            ->orderBy($order, $dir)
            ->get();

        foreach ($clients as $client)
        {
            $sales = Sale::where('deleted_at', '=', null)
                ->where('client_id', $client->id)
                ->select(['id','client_id','GrandTotal', 'paid_amount'])
                ->get();

            // total sales
            $item['total_sales'] = $sales->sum('GrandTotal');
            $item['total_payments'] = PaymentSale::whereNull('deleted_at')->where('client_id', $client->id)->whereNotNull('sale_id')->sum('montant') ?? 0;
            $item['total_return'] = PaymentSale::whereNull('deleted_at')->where('client_id', $client->id)->whereNull('sale_id')->sum('montant') ?? 0;

            $item['due'] = $item['total_sales'] - $item['total_payments'] - $item['total_return'];

            $item['id'] = $client->id;
            $item['name'] = $client->name;
            $item['phone'] = $client->phone;
            $item['tax_number'] = $client->tax_number;
            $item['code'] = $client->code;
            $item['email'] = $client->email;
            $item['country'] = $client->country;
            $item['city'] = $client->city;
            $item['adresse'] = $client->adresse;
            $item['localisation'] = $client->localisation;
            $item['zone'] = $client->zone;
            $item['strict_credit'] = $client->strict_credit;
            $item['max_credit'] = $client->max_credit;

            if ($client->user)
                $client->user->fullname = $client->user->firstname . " " . $client->user->lastname;
            $item['user'] = $client->user;
            $data[] = $item;
        }
        $company_info = Setting::where('deleted_at', '=', null)->first();
        $vendors = User::whereIn('role_id', [2, 4])->orWhere('id', 1)->get();
        return response()->json([
            'clients' => $data,
            'company_info' => $company_info,
            'totalRows' => $totalRows,
            'vendors' => $vendors
        ]);
    }

    public function app_index(request $request)
    {
        // $this->authorizeForUser($request->user('api'), 'view', Client::class);
        $current_user = Auth::user();

        // How many items do you want to display.
        $perPage = $request->limit;
        $pageStart = \Request::get('page', 1);

        // Start displaying items from this number;
        $offSet = ($pageStart * $perPage) - $perPage;
        $order = $request->SortField;
        $dir = $request->SortType;

        $data = array();
        $clients = Client::select(['id','code','name','phone', 'city','localisation','tax_number', 'adresse', 'zone', 'strict_credit', 'max_credit'])->whereNull('deleted_at');

        if (empty($current_user) || $current_user->role_id == 2 || $current_user->role_id == 4)
            $clients = $clients->where('id_user', $current_user->id);

        // Search With Multiple Param
        if (!empty($request->search) && $request->search != "")
        {
            $search_value = $request->search;

            $clients = $clients->where(function($query) use ($search_value){
                $query->where('code', 'LIKE', "%{$search_value}%")
                    ->orWhere('name', 'LIKE', "%{$search_value}%");
            });
        }

        $totalRows = $clients->count();

        // Filter
        $clients = $clients->offset($offSet)
            ->limit($perPage)
            ->orderBy($order, $dir)
            ->get();

        foreach ($clients as $client)
        {
            $sales = Sale::whereNull('deleted_at')
                ->where('client_id', $client->id)
                ->select(['id','client_id','GrandTotal', 'paid_amount'])
                ->get();

            // total sales
            $item['total_sales'] = $sales->sum('GrandTotal');
            $item['total_payments'] = PaymentSale::whereNull('deleted_at')->where('client_id', $client->id)->whereNotNull('sale_id')->sum('montant') ?? 0;
            $item['total_return'] = PaymentSale::whereNull('deleted_at')->where('client_id', $client->id)->whereNull('sale_id')->sum('montant') ?? 0;

            $item['due'] = $item['total_sales'] - $item['total_payments'] - $item['total_return'];

            $item['id'] = $client->id;
            $item['localisation'] = $client->localisation;
            $item['name'] = $client->name;
            $item['adresse'] = $client->adresse;
            $item['phone'] = $client->phone;
            $item['code'] = $client->code;
            $item['city'] = $client->city;
            $item['tax_number'] = $client->tax_number;
            $item['zone'] = $client->zone;
            $item['strict_credit'] = $client->strict_credit;
            $item['max_credit'] = $client->max_credit;

            $data[] = $item;
        }

        return response()->json([
            'data' => $data,
            'totalPages' => ($totalRows/$request->limit),
        ]);
    }


    public function client_credit(request $request)
    {
        $this->authorizeForUser($request->user('api'), 'view', Client::class);
        // How many items do you want to display.

        $data = array();
        $user_id = Auth::user()->id;

        if ($user_id !== 1)
            $clients = Client::with("user")->where('id_user', $user_id)->where('deleted_at', '=', null);
        else
            $clients = Client::with("user")->where('deleted_at', '=', null);

        $clients = $clients->orderBy('name', 'asc')->get();


        foreach ($clients as $client)
        {
            $sales = Sale::where('deleted_at', '=', null)
                ->where('client_id', $client->id)
                ->select(['id','client_id','GrandTotal', 'paid_amount'])
                ->get();

            // total sales
            $item['total_amount'] = $sales->sum('GrandTotal');
            $item['total_paid'] = PaymentSale::whereNull('deleted_at')->where('client_id', $client->id)->whereNotNull('sale_id')->sum('montant') ?? 0;
            $item['total_paid_return'] = PaymentSale::whereNull('deleted_at')->where('client_id', $client->id)->whereNull('sale_id')->sum('montant') ?? 0;

            $item['due'] = $item['total_amount'] - $item['total_paid'] - $item['total_paid_return'];

            $item['id'] = $client->id;
            $item['name'] = $client->name;

            $data[] = $item;
        }

        return response()->json([
            'data' => $data,
            'status' => true
        ]);
    }

    //------------ function show -----------\\

    public function app_show($id)
    {
        if (empty($id) || is_nan($id))
        {
            return response()->json([
             'status' => false,
             'data' => null,
            ]);
        }

        $client = Client::where('deleted_at', '=', null)->findOrFail($id);

        if(is_null($client) || empty($client))
        {
             return response()->json([
                 'status' => false,
                 'message' => 'client not found',
             ]);
        }

        /////-----  list sales
        $sales = Sale::
            where('deleted_at', '=', null)
            ->where('client_id', $id)
            ->select([
                'id',
                'user_id',
                'date',
                'Ref',
                'warehouse_id',
                'GrandTotal',
                'paid_amount',
                'payment_statut',
                'statut',
                'notes',
                'created_at',
                DB::raw('(CASE WHEN payment_statut = "unpaid" AND DATEDIFF(NOW(), date) > 30 THEN true ELSE false end) as passed_month')
            ])
            ->limit(30)
        ->get()->toArray();

        /////-----  list of Payments
        $payments = PaymentSale::
            with('sale.client')
            ->select(
                'payment_sales.date',
                'payment_sales.Ref AS Ref',
                'sales.Ref AS Sale_Ref',
                'payment_sales.Reglement',
                'payment_sales.montant',
                DB::raw('SUM(montant) as total_payment')
            )
            ->groupBy('Ref')
            ->whereNull('payment_sales.deleted_at')
            ->join('sales', 'payment_sales.sale_id', '=', 'sales.id')
            ->where('sales.client_id', $id)
            ->limit(30)
        ->get()->toArray();

        /////-----  list of Return
        $returns = SaleReturn::
            where('deleted_at', '=', null)
            ->where('client_id', $id)
            ->select(
                'id',
                'Ref',
                'date',
                'GrandTotal',
                'user_id',
                'notes',
                'statut',
                'created_at'
            )
            ->limit(30)
        ->get()->toArray();

        return response()->json([
            'status' => true,
            'client' => $client,
            'data_sales' => $sales,
            'data_payments' => $payments,
            'data_returns' => $returns,
        ]);
    }


    //------------- Store new Customer -------------\\

    public function store(Request $request)
    {
        $this->authorizeForUser($request->user('api'), 'create', Client::class);

        $this->validate($request, [
            'name' => 'required',
            ]
        );

        Client::create([
            'name' => $request['name'],
            'code' => $this->getNumberOrder(),
            'adresse' => $request['adresse'],
            'phone' => $request['phone'],
            'email' => $request['email'],
            'country' => $request['country'],
            'city' => $request['city'],
            'tax_number' => $request['tax_number'],
            'id_user' => $request['user_id'],
            'localisation' => $request['localisation'],
            'zone' => $request['zone'],
            'strict_credit' => $request['strict_credit'] ?? 0,
            'max_credit' => $request['max_credit'],
        ]);
        return response()->json(['success' => true]);
    }

    public function update_location(Request $request)
    {
        $this->authorizeForUser($request->user('api'), 'update', Client::class);

        $this->validate($request, [
                'id' => 'required|integer',
                'localisation' => 'required',
            ]
        );

        $updatedRows  = Client::whereId($request['id'])->update([
            'localisation' => $request['localisation']
        ]);

        if ($updatedRows > 0)
            return response()->json(['success' => true]);
        else
            return response()->json(['success' => false, 'message' => 'Update failed'], 400);
    }



    //------------ function show -----------\\

    public function show($id){
        //

    }

    //------------- Update Customer -------------\\

    public function update(Request $request, $id)
    {
        $this->authorizeForUser($request->user('api'), 'update', Client::class);

        $this->validate($request, ['name' => 'required',]);

        Client::whereId($id)->update([
            'name' => $request['name'],
            'adresse' => $request['adresse'],
            'phone' => $request['phone'],
            'email' => $request['email'],
            'country' => $request['country'],
            'city' => $request['city'],
            'tax_number' => $request['tax_number'],
            'id_user' => $request['user_id'],
            'zone' => $request['zone'],
            'strict_credit' => $request['strict_credit'],
            'max_credit' => $request['max_credit'],
        ]);

        return response()->json(['success' => true]);
    }

    public function update_app(Request $request, $id)
    {
        $this->authorizeForUser($request->user('api'), 'update', Client::class);

        $this->validate($request, ['name' => 'required',]);

        // Get all request data except for the _method and _token
        $requestData = $request->except(['_method', '_token']);

        // Update the client only with the provided columns in the request
        $updatedColumns = array_filter($requestData, function ($value) {
            return $value !== null;
        });

        Client::whereId($id)->update($updatedColumns);

        return response()->json(['success' => true]);
    }

    //------------- delete client -------------\\

    public function destroy(Request $request, $id)
    {
        $this->authorizeForUser($request->user('api'), 'delete', Client::class);

        Client::whereId($id)->update([
            'deleted_at' => Carbon::now(),
        ]);
        return response()->json(['success' => true]);
    }

    //-------------- Delete by selection  ---------------\\

    public function delete_by_selection(Request $request)
    {
        $this->authorizeForUser($request->user('api'), 'delete', Client::class);
        $selectedIds = $request->selectedIds;

        foreach ($selectedIds as $Client_id) {
            Client::whereId($Client_id)->update([
                'deleted_at' => Carbon::now(),
            ]);
        }
        return response()->json(['success' => true]);
    }


    //------------- get Number Order Customer -------------\\

    public function getNumberOrder()
    {
        $last = DB::table('clients')->latest('id')->first();

        if ($last) {
            $code = $last->code + 1;
        } else {
            $code = 1;
        }
        return $code;
    }

    //------------- Get Clients Without Paginate -------------\\

    public function Get_Clients_Without_Paginate()
    {
        $clients = Client::where('deleted_at', '=', null)->get(['id', 'name']);
        return response()->json($clients);
    }

    // import clients
    public function import_clients(Request $request)
    {
        $file_upload = $request->file('clients');
        $ext = pathinfo($file_upload->getClientOriginalName(), PATHINFO_EXTENSION);
        if ($ext != 'csv') {
            return response()->json([
                'msg' => 'must be in csv format',
                'status' => false,
            ]);
        } else {
            $data = array();
            $rowcount = 0;
            if (($handle = fopen($file_upload, "r")) !== false) {
                $max_line_length = defined('MAX_LINE_LENGTH') ? MAX_LINE_LENGTH : 10000;
                $header = fgetcsv($handle, $max_line_length);
                $header_colcount = count($header);
                while (($row = fgetcsv($handle, $max_line_length)) !== false) {
                    $row_colcount = count($row);
                    if ($row_colcount == $header_colcount) {
                        $entry = array_combine($header, $row);
                        $data[] = $entry;
                    } else {
                        return null;
                    }
                    $rowcount++;
                }
                fclose($handle);
            } else {
                return null;
            }

            $rules = array('name' => 'required');

            //-- Create New Client
            foreach ($data as $key => $value) {
                $input['name'] = $value['name'];

                $validator = Validator::make($input, $rules);
                if (!$validator->fails()) {

                    Client::create([
                        'name' => $value['name'],
                        'code' => $this->getNumberOrder(),
                        'adresse' => $value['adresse'] == '' ? null : $value['adresse'],
                        'phone' => $value['phone'] == '' ? null : $value['phone'],
                        'email' => $value['email'] == '' ? null : $value['email'],
                        'country' => $value['country'] == '' ? null : $value['country'],
                        'city' => $value['city'] == '' ? null : $value['city'],
                        'tax_number' => $value['tax_number'] == '' ? null : $value['tax_number'],
                    ]);

                }


            }

            return response()->json([
                'status' => true,
            ], 200);
        }

    }


     //------------- clients_pay_due -------------\\

     public function clients_pay_due(Request $request)
     {
         $this->authorizeForUser($request->user('api'), 'pay_due', Client::class);

         if($request['amount'] > 0)
         {
            $client_sales_due = Sale::where('deleted_at', '=', null)
            ->where([
                ['payment_statut', '!=', 'paid'],
                ['client_id', $request->client_id]
            ])->get();

            $paid_amount_total = $request->amount;

            // Added by oussama, so all added payment get same ref
            $generated_ref = app('App\Http\Controllers\PaymentSalesController')->getNumberOrder();

            foreach($client_sales_due as $key => $client_sale)
            {
                if($paid_amount_total == 0)
                    break;
                $due = $client_sale->GrandTotal  - $client_sale->paid_amount;

                if($paid_amount_total >= $due){
                    $amount = $due;
                    $payment_status = 'paid';
                }else{
                    $amount = $paid_amount_total;
                    $payment_status = 'partial';
                }

                $payment_sale = new PaymentSale();
                $payment_sale->sale_id = $client_sale->id;
                $payment_sale->Ref = $generated_ref;
                $payment_sale->date = Carbon::now();
                $payment_sale->Reglement = $request['Reglement'];
                $payment_sale->montant = $amount;
                $payment_sale->change = 0;
                $payment_sale->notes = $request['notes'];
                $payment_sale->user_id = Auth::user()->id;
                $payment_sale->save();

                $client_sale->paid_amount += $amount;
                $client_sale->payment_statut = $payment_status;
                $client_sale->save();

                $paid_amount_total -= $amount;
            }
        }

         return response()->json(['success' => true]);

     }

    public function app_clients_pay_due(Request $request)
    {
        $this->authorizeForUser($request->user('api'), 'pay_due', Client::class);

        if ($request['amount'] <= 0)
            response()->json(['success' => false, 'message' => "invalid input"]);


        $client_sales_due = Sale::where('deleted_at', '=', null)
        ->where([
            ['payment_statut', '!=', 'paid'],
            ['client_id', $request->client_id]
        ])->get();

        $paid_amount_total = $request->amount;

        // Added by oussama, so all added payment get same ref
        $generated_ref = app('App\Http\Controllers\PaymentSalesController')->getNumberOrder();

        foreach($client_sales_due as $key => $client_sale)
        {
            if($paid_amount_total == 0)
                break;
            $due = $client_sale->GrandTotal  - $client_sale->paid_amount;

            if($paid_amount_total >= $due)
            {
                $amount = $due;
                $payment_status = 'paid';
            }
            else
            {
                $amount = $paid_amount_total;
                $payment_status = 'partial';
            }

            $current_user = Auth::user();

            $payment_sale = new PaymentSale();
            $payment_sale->sale_id = $client_sale->id;
            $payment_sale->Ref = $generated_ref;
            $payment_sale->date = Carbon::now();
            $payment_sale->Reglement = $request['Reglement'];
            $payment_sale->montant = $amount;
            $payment_sale->change = 0;
            $payment_sale->notes = $request['notes'];
            $payment_sale->user_id = $current_user->id;
            $payment_sale->payment_received = ($current_user->id === 1) ? null : 0;
            $payment_sale->client_id = $request->client_id;

            $payment_sale->save();

            $client_sale->paid_amount += $amount;
            $client_sale->payment_statut = $payment_status;
            $client_sale->save();

            $paid_amount_total -= $amount;
        }


        return response()->json(['success' => true]);

    }



    //------------- clients_pay_sale_return_due -------------\\

    public function pay_sale_return_due(Request $request)
    {
        $this->authorizeForUser($request->user('api'), 'pay_sale_return_due', Client::class);

        if($request['amount'] > 0){
            $client_sell_return_due = SaleReturn::where('deleted_at', '=', null)
            ->where([
                ['payment_statut', '!=', 'paid'],
                ['client_id', $request->client_id]
            ])->get();

            $paid_amount_total = $request->amount;

            foreach($client_sell_return_due as $key => $client_sale_return){
                if($paid_amount_total == 0)
                break;
                $due = $client_sale_return->GrandTotal  - $client_sale_return->paid_amount;

                if($paid_amount_total >= $due){
                    $amount = $due;
                    $payment_status = 'paid';
                }else{
                    $amount = $paid_amount_total;
                    $payment_status = 'partial';
                }

                $payment_sale_return = new PaymentSaleReturns();
                $payment_sale_return->sale_return_id = $client_sale_return->id;
                $payment_sale_return->Ref = app('App\Http\Controllers\PaymentSaleReturnsController')->getNumberOrder();
                $payment_sale_return->date = Carbon::now();
                $payment_sale_return->Reglement = $request['Reglement'];
                $payment_sale_return->montant = $amount;
                $payment_sale_return->change = 0;
                $payment_sale_return->notes = $request['notes'];
                $payment_sale_return->user_id = Auth::user()->id;
                $payment_sale_return->save();

                $client_sale_return->paid_amount += $amount;
                $client_sale_return->payment_statut = $payment_status;
                $client_sale_return->save();

                $paid_amount_total -= $amount;
            }
        }

        return response()->json(['success' => true]);

    }

}
