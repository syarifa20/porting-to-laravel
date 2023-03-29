<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DetailCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Exception;
use stdClass;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->input('filters', []);
        $limit = $request->input('rows', 10); // ubah input limit menjadi rows
        $page = $request->input('page', 1);
        $sidx = $request->input('sidx'); // ubah sort_index menjadi sidx
        $sord = $request->input('sord', 'asc');

        $search = request()->has('_search'); // search flag

        if (!$sidx) $sidx = 'nama';

        $query = DB::table('customers');

        $global_search = request()->input('global_search'); // ubah global_search menjadi searchField
        if ($global_search) {
            $global_search = '%' . $global_search . '%';
            $query->where(function ($query) use ($global_search) {
                $query->where('no_invoice', 'LIKE', $global_search)
                    ->orWhere('nama', 'LIKE', $global_search)
                    ->orWhere('tgl_pembelian', 'LIKE', $global_search)
                    ->orWhere('saldo', 'LIKE', $global_search)
                    ->orWhere('gender', 'LIKE', $global_search);
            });
        }

        $filterResultsJSON = request()->input('filters');
        if ($filterResultsJSON) {
            $filterResults = json_decode($filterResultsJSON, true);
            if (isset($filterResults['rules']) && is_array($filterResults['rules'])) {
                foreach ($filterResults['rules'] as $filterRules) {
                    $query->where($filterRules['field'], 'LIKE', '%' . $filterRules['data'] . '%');
                }
            }
        }

        $count = $query->count();
        $total_pages = ($count > 0 && $limit > 0) ? ceil($count/$limit) : 0;

        $start = $limit * $page - $limit;

        $rows = $query->orderBy($sidx, $sord)->offset($start)->limit($limit)->get()->toArray(); 
        $response = new stdClass();
        $response->page = $page;
        $response->total = $total_pages;
        $response->records = $count;
        $response->rows = $rows;

        $data = $response;
       
        return response()->json($response);
        // return view ('customers.index', ['data' => $data]);
       
    }

    public function showData(){
        return view ('customers.index');
    }

    public function showDialogAdd()
    {
        return view('customers.form.create');
    }

    public function getPosition(){
        echo 'berhasil';
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_invoice' => 'required',
            'customer_name' => 'required',
            'tgl_pembelian' => 'required',
            'saldo' => 'required'
        ]);
        
        try {
            DB::beginTransaction();
        
            $customer = new Customer();
            $customer->no_invoice = $request->input('no_invoice');
            $customer->nama = $request->input('customer_name');
            $customer->tgl_pembelian = date('Y-m-d', strtotime($request->input('tgl_pembelian')));
            $customer->saldo = intval(str_replace(".", "", $request->input('saldo')));
            $customer->gender = $request->input('gender_id');
            $customer->save();
        
            $id = $customer->id;

        
            if ($request->filled('item_name')) {
                foreach ($request->input('item_name') as $index => $item_name) {
                    $detail_customer = new DetailCustomer();
                    $detail_customer->nama_brg = $item_name;
                    $detail_customer->qty = $request->input('qty')[$index];
                    $detail_customer->harga = str_replace(".", "", $request->input('item_price')[$index]);
                    $detail_customer->customer_id = $id;
                    $detail_customer->save();
                }
            }
           
            DB::commit();
            $res = $id;
            return response()->json($res);
        
        } catch (Exception $e) {
            DB::rollback();
            $res = [
                'status' => 500,
                'message' => $e->getMessage()
            ];
            return response()->json($res);
        }
    }

    public function showDialogUpdate($id_customer){
        $customers = DB::table('customers')
                ->leftjoin('detail_customers', 'customers.id_customer', '=', 'detail_customers.customer_id')
                ->where('customers.id_customer', '=', $id_customer)
                ->get();
       
        return view('customers.form.edit', ['customers'=> $customers]);
    }

    public function update(Request $request){
        // Validate input
        $request->validate([
            'no_invoice' => 'required',
            'customer_name' => 'required',
            'saldo' => 'required',
            'gender_id' => 'required',
        ]);
    
        $id_customer = $request->input('id_customer');
        $no_invoice = $request->input('no_invoice');
        $customer_name = $request->input('customer_name');
        $tgl_pembelian = $request->input('tgl_pembelian');
        $date = date('Y-m-d', strtotime($tgl_pembelian));
        $saldo = $request->input('saldo');
        $gender_id = $request->input('gender_id');
    
        $nama_brg = $request->input('item_name');
        $qty = $request->input('qty');
        $harga = $request->input('item_price');
    
        $num = intval(floatval(str_replace(",", ".", $saldo)) * 100);
    
        try {
            DB::beginTransaction();
    
            $customer = Customer::where('id_customer', $id_customer)->firstOrFail();
            $customer->no_invoice = $no_invoice;
            $customer->nama = $customer_name;
            $customer->tgl_pembelian = $date;
            $customer->saldo = $num;
            $customer->gender = $gender_id;
            $customer->save();
    
           
    
            if ($request->filled('item_name')){
                DetailCustomer::where('customer_id', $id_customer)->delete();
                foreach ($nama_brg as $index => $item_name) {
                    $detail_customer = new DetailCustomer();
                    $detail_customer->nama_brg = $item_name;
                    $detail_customer->qty = $qty[$index];
                    $detail_customer->harga = preg_replace('/[^0-9]/', '', $harga[$index]);
                    $detail_customer->customer_id = $id_customer;
                    $detail_customer->save();
                }
            }
    
            DB::commit();
            $res = $id_customer;
            return response()->json($res);
        } catch (Exception $ex) {
            DB::rollback();
            $res = [
                'status' => 500,
                'message' => $ex->getMessage()
            ];
            return response()->json($res);
        }
    } 
    
    public function showDialogDelete($id_customer){
        $customers = DB::table('customers')
                ->leftjoin('detail_customers', 'customers.id_customer', '=', 'detail_customers.customer_id')
                ->where('customers.id_customer', '=', $id_customer)
                ->get();
       
        return view('customers.form.hapus', ['customers'=> $customers]);
    }

    public function delete(){
        $id_customer = request()->input('id_customer');
        // DB::beginTransaction();
        try {
            DB::beginTransaction();

            DB::table('customers')->where('id_customer', $id_customer)->delete();
            DB::table('detail_customers')->where('customer_id', $id_customer)->delete();

            DB::commit();
            $res = $id_customer;
            return response()->json($res);
        } catch (Exception $ex) {
            DB::rollBack();
            $res = [  'status' => 500,        
                      'message' => $ex->getMessage() 
                   ];
            return response()->json($res);
        }

    }

    public function detailMaster(Request $request, $id_customer){
            $detailCustomers = DB::table('detail_customers')
                ->select('nama_brg', 'qty', 'harga')
                ->where('customer_id', $id_customer)
                ->get();
            // dd($detailCustomers);
            
            if($detailCustomers->count() > 0) {
                $result = [];
                foreach($detailCustomers as $detail) {
                    $total_price = $detail->qty * $detail->harga;
                    $result[] = [
                        'nama_brg' => $detail->nama_brg,
                        'qty' => $detail->qty,
                        'item_price' => $detail->harga,
                        'total_price' => $total_price 
                    ];
                }
                return response()->json($result);
            } else {
                return response()->json([]);
            }
       
      
    }

    public function report(Request $request){
        $sidx = $request->input('sidx');
        $sord = $request->input('sord');
        $global_search = $request->input('global_search');
        $filters = $request->input('filters') ? json_decode($request->input('filters')) : null;
        $start = $request->input('start');
        $limit = $request->input('limit');

        $query = DB::table('customers');
        $query->select('*');

        if ($global_search) {
            $query->where(function ($q) use ($global_search) {
                $q->where('no_invoice', 'like', '%' . $global_search . '%')
                ->orWhere('nama', 'like', '%' . $global_search . '%')
                ->orWhere('tgl_pembelian', 'like', '%' . $global_search . '%')
                ->orWhere('saldo', 'like', '%' . $global_search . '%')
                ->orWhere('gender', 'like', '%' . $global_search . '%');
            });
        }
       

        if ($filters && is_array($filters->rules)) {
            foreach ($filters->rules as $filterRules) {
                $filterRules = (array) $filterRules;
                $query->where($filterRules['field'], 'like', '%' . $filterRules['data'] . '%');
            }
        }

        $count = $query->count();
        $offset = $limit-$start+1;
        if ($offset) {
            $query->offset($start - 1)->limit($offset);
        }

        $query->orderBy($sidx, $sord);

        $results = $query->get();
        // dd($query);

        $tempdata = [];

        foreach ($results as $index => $dataSales) {
            $queryDetail = DB::table('detail_customers')
                ->where('customer_id', $dataSales->id_customer)
                ->get();
            $numRows = $queryDetail->count();
            $no = 1;
    
            $salesDetail = [];
    
            foreach ($queryDetail as $dataDetail) {
                $dataDetail->no = $no++;
                $salesDetail[] = (array)$dataSales + (array)$dataDetail;
            }
    
            if ($numRows == 0) {
                $salesDetail[] = (array)$dataSales;
            }
    
            $tempdata['sales'] = array_merge($tempdata['sales'] ?? [], $salesDetail);
            
            foreach ($tempdata['sales'] as &$sale) {
                $sale['tgl_pembelian'] = date('d-m-Y', strtotime($sale['tgl_pembelian']));
            }
        }
        // dd($tempdata);

       
        return view('customers.report', ['data' => $tempdata]);
    }

    public function export(){
        $id_customer = request('id_customer');
        $sidx = request('sidx', 'id');
        $sord = request('sord', 'asc');
        $global_search = request('global_search');
        $filters = request('filters') ? json_decode(request('filters')) : null;
        $search = request()->has('_search');

        $query = Customer::query();
        $query->where('id_customer', $id_customer);

        if ($global_search) {
            $query->where(function ($query) use ($global_search) {
                $query->where('no_invoice', 'like', '%' . $global_search . '%')
                    ->orWhere('nama', 'like', '%' . $global_search . '%')
                    ->orWhere('tgl_pembelian', 'like', '%' . $global_search . '%')
                    ->orWhere('saldo', 'like', '%' . $global_search . '%')
                    ->orWhere('gender', 'like', '%' . $global_search . '%');
            });
        }

        if ($filters) {
            foreach ($filters->rules as $rule) {
                $query->where($rule->field, 'like', '%' . $rule->data . '%');
            }
        }

        $count = $query->count();

        $query->orderBy($sidx, $sord);

        $results = $query->get();

        $tempdata = [];
        foreach ($results as $index => $dataSales) {
            $queryDetail = DB::table('detail_customers')
                ->where('customer_id', $dataSales->id_customer)
                ->get();
            $numRows = $queryDetail->count();
            $no = 1;
    
            $salesDetail = [];
    
            foreach ($queryDetail as $dataDetail) {
                $dataDetailArray = get_object_vars($dataDetail);
                $dataDetailArray['no'] = $no++;
                $salesDetail[] = $dataSales->toArray() + $dataDetailArray;
            }
        
        }

        return view('customers.export', ['data' => $salesDetail]);
    }

    }
       
   

   

