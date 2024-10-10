<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Models\User;
use App\Models\Item;
use App\Helpers\Helper;
use Hash;
use PDF;

class ItemController extends Controller
{

    // form view
    public function inventory()
    {
        if(Auth::check()){
            $data = Item::sortable()->whereIn('status', ['Available', 'Unresolved' , 'Lost'])->paginate(10);
            return view('inventory',['items'=>$data]);
        }

        return redirect("login")->with('noaccess','Please login to access that page');
    }

    // form view
    public function checkout()
    {

        if(Auth::check()){
            $data = Item::sortable()->whereIn('status', ['Checked Out'])->paginate(10);
            return view('checkout',['items'=>$data]);
        }

        return redirect("login")->with('noaccess','Please login to access that page');

    }


    // form view
    public function checkin()
    {
        if(Auth::check()){
            return view('checkin');
        }

        return redirect("login")->with('noaccess','Please login to access that page');
    }

            /**
     * Write code on Method
     *
     * @return response()
     */
    public function postAddItem(Request $request){

        $request->validate([
            'item_code' => 'unique:items',
        ]);

        $data = $request->all();
        $check = $this->create($data);

        $item_code=$data['item_code'];
        $item = Item::where('item_code', $item_code)->first();

        $id = $item['id'];
        return redirect()->route('viewitem', [$id]);
    }

            /**
     * Write code on Method
     *
     * @return response()
     */
    public function create(array $data)
    {
      return Item::create([
        'item_name' => $data['item_name'],
        'item_code' => $data['item_code'],
        // 'quantity' => $data['quantity'],
        'command' => $data['command'],
        // 'location' => '-',
        // 'checkInDate' => $data['checkInDate'],
        // 'checkOutDate' => $data['checkOutDate'],
        'status' => 'Available',
        // 'release' => 'On time'
      ]);
    }


            /**
     * Write code on Method
     * @param  \App\Item  $item
     * @return response()
     */
    public function viewitem($id)
    {
        if(Auth::check()){
            $item = Item::where('id', $id)->first();
            return view('viewitem', compact('item'));
        }

        return redirect("login")->with('noaccess','Please login to access that page');
    }


                /**
     * Write code on Method
     * @param  \App\Item  $item
     * @return response()
     */
    public function edititem(Request $request)
    {
        $data = Item::find($request->id);

        $data->item_name=$request->item_name;
        $data->item_code=$request->item_code;
        // $data->quantity=$request->quantity;
        $data->command=$request->command;
        // $data->location=$request->location;
        $data->status=$request->status;
        // $data->checkInDate=$request->checkInDate;
        // $data->checkOutDate=$request->checkOutDate;

        $data->save();

        return back();
    }


                /**
     * Write code on Method
     * @param  \App\Item  $item
     * @return response()
     */
    public function checkoutitem(Request $request)
    {
        $data = Item::find($request->id);
        $data->status=$request->status;

        $data->save();

        return redirect('inventory');
    }


                /**
     * Write code on Method
     * @param  \App\Item  $item
     * @return response()
     */
    public function itempdf($id){

        $item = Item::where('id', $id)->first();
        $pdf = PDF::loadView('itempdf',compact('item'));
        return $pdf->stream();

    }



                /**
     * Write code on Method
     * @param  \App\Item  $item
     * @return response()
     */
    public function viewcheckout($id)
    {
        if(Auth::check()){
            $item = Item::where('id', $id)->first();
            return view('viewcheckout', compact('item'));
        }

        return redirect("login")->with('noaccess','Please login to access that page');
    }


                    /**
     * Write code on Method
     * @param  \App\Item  $item
     * @return response()
     */
    public function recheckinitem(Request $request)
    {
        $data = Item::find($request->id);
        $data->status=$request->status;

        $data->save();

        return redirect('checkout');
    }


                    /**
     * Write code on Method
     * @param  \App\Item  $item
     * @return response()
     */
    public function deleteitem(Request $request)
    {
        $data = Item::find($request->id);
        $data->delete();

        return redirect('checkout');
    }

}
