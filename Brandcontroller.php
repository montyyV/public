<?php

namespace App\Http\Controllers\Customer;
use App\Customer;
use App\Partner_field_form;
use App\partner_contact_field;
use App\Partner_property;
use Mail; 
use App\Model\amail_template_manage;
use App\Model\email_template_it_manage;
use App\social_page;
use App\Model\audience;
use Illuminate\Http\Request;  
use App\Http\Controllers\Controller;
use App\Model\manage_social_media;
use App\Model\manage_county;
use App\Model\formate;
use App\Model\company;
use App\Model\my_formate;
use App\Model\Target_Sport;
use App\Model\whonotsee;
use Auth;
use App\Model\reset_password;
use App\Model\page;
use App\Model\kpi;
use App\Model\offer;
use App\Model\orders;
use App\Model\chat;
use Validator;
use App\Model\action_time;
use DB;
use Carbon\Carbon;
use App\Model\state;
use Config;
use App\Model\chat_invoice;
use App\Model\social_data_pro;
use App\Model\invoice;
use App\Model\Brand;
use App\Model\Advertiser;
use App\Model\price_type;
use App\Model\typeformate;
use App\Http\Controllers\Customer\fpdf;
use App\Http\Controllers\Customer\PDF_HTML;
use App\Model\alert_timer;
use App\Model\vat;
use Session;
use App\Model\amail_template_manages_ita;
use App\Model\formate_ita;
use App\Model\manage_county_ita;
use App\Model\manage_social_media_ita;
use App\Model\Target_Sport_ita;
use App\Model\ts_cat_ita;
use App\Model\typeformate_ita;
use App\Model\audience_ita;
use App\Model\pq_history;
use App\Model\Kpi_values;
use App\Model\chat_todo;
use App\Model\todo;
use App\Model\media_api_data;
use App\Model\state_ita;
use App\Model\BrandAsignroles;
use Socialite;
class Brandcontroller extends Controller
{




  public function __construct(){

  	
		$this->middleware(function ($request, $next) {
	  
		$data = session()->get( 'locale' );
	  if ($data=='en') {
		$this->email_template_mana = "App\Model\amail_template_manage";
		$this->formate = 'App\Model\formate';
		$this->manage_county = "App\Model\manage_county";
		$this->manage_social_media = "App\Model\manage_social_media";
		$this->Target_Sport = "App\Model\Target_Sport";
		$this->ts_cat = 'App\Model\ts_cat';
		$this->typeformate = 'App\Model\typeformate';
		$this->audience = "App\Model\audience";
    $this->page = "App\Model\page";
    $this->state = "App\Model\state";
        $this->price_type = "App\Model\price_type";
    $this->permissions = 'App\Model\roles';
    $this->role = 'App\Model\role_name';
	  }else if($data=='ita'){
	  $this->email_template_mana = "App\Model\amail_template_manages_ita";
	  $this->formate = 'App\Model\formate_ita';
	  $this->manage_county = "App\Model\manage_county_ita";
	  $this->manage_social_media = "App\Model\manage_social_media_ita";
	  $this->Target_Sport = "App\Model\Target_Sport_ita";
	  $this->ts_cat = 'App\Model\ts_cat_ita';
	  $this->typeformate = 'App\Model\typeformate_ita';
	  $this->audience = "App\Model\audience_ita";
    $this->page = "App\Model\page_it";
    $this->state = "App\Model\state_ita";
        $this->price_type = "App\Model\price_type_ita";
    $this->permissions = 'App\Model\roles_ita';
    $this->role = 'App\Model\role_name_ita';
	  }
					  return $next($request);
			  });



		  }


      function RemoveSpecialChar($str) {
      
        // Using str_replace() function 
        // to replace the word 
        $res = str_replace( array( '\'', '"'
         , ';', '<', '>','&' ,'(',')'), ['','','','','','','',''], $str);
          
        // Returning the result 
        return $res;
        }


public function addbrand()
{

$data['country'] = $this->manage_county::where('status',0)->get();
$data['adver'] = \App\Model\company::with('countryc')->where('company_type','Advertiser')->get();
return view('customer.Brand.add-brand',$data);

}

public function add_brand_form(Request $request)
{


  $request->validate([

            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'name' => 'required',
                 'country' => 'required',
                 'adver'=>'required',
              


        ]);

 $imageName = time().'.'.$request->image->extension();  
 $request->image->move(public_path('brandlogos'), $imageName);

 $rwc =  Brand::where('name',$request->name)->where('country',$request->country)->count();

if($rwc>0){
return 22;
}else{

$user = Brand::create([
'name'=>$request->name,
'status'=>1,
'namea'=>$request->adver,
'logo'=>$imageName,
'country'=>$request->country,
'user_id'=>auth()->user()->id,
]);

BrandAsignroles::create([
  'main_owner_id'=>auth()->user()->id,
  'owner_id'=>auth()->user()->id,
  'customer_id'=>auth()->user()->id,
  'brand_campaign_id'=>$user->id,
  'status'=>1,
  'role'=>5,
  'type'=>null,
]);

return redirect('customer/viewbrand')->with('success','Successfully Added Brand');
}

}

public function update_brand_form(Request $request,$id)
{
if(!empty($request->image)){

	  $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'name' => 'required',
                 'country' => 'required',
        ]);

 $imageName = rand(45,66).time().'.'.$request->image->extension();  
 $request->image->move(public_path('brandlogos'), $imageName);

    }else{
    $imageName = $request->image_o; 	
    }


     $rwc =  Brand::where('id','!=',$id)->where('name',$request->name)->where('country',$request->country)->where('user_id',auth()->user()->id)->count();

if($rwc>0){
return 22;
}else{
	
	Brand::where('id',$id)->update([
'name'=>$request->name,
'status'=>0,
'namea'=>$request->adver,
'logo'=>$imageName,
'country'=>$request->country,
'user_id'=>auth()->user()->id,
]);
return 200;
}
}
  //brand detail
public function viewbrand()
{
	$data['data'] = \App\Model\Brand::where('user_id',auth()->user()->id)->get();
$data['country']  =	  $this->manage_county ::get();
return view('customer.Brand.brand',$data);
}

public function updatebrand($id)
{
	$data['adver'] = \App\Model\company::with('countryc')->where('company_type','Advertiser')->get();
		$data['data'] = \App\Model\Brand::where('id',$id)->first();
$data['country'] = $this->manage_county::where('status',0)->get();
return view('customer.Brand.edit-brand',$data);
}


  // company advertiser data for select field
public function fetch_compay(Request $request)
{

$datacp = \App\Model\company::where('id',$request->data)->first();

 $datacpp = \App\Model\manage_county::where('id',$datacp['country'])->first();

$datac = "  <tr>
                   <th>Name</th>
                   <td>".$datacp['name']."</td>
               </tr>
                <tr>
                   <th>Address</th>
                   <td>".$datacp['address']."</td>
               </tr>
                <tr>
                   <th>Country</th>
                   <td>
                   ".$datacpp['manage_county']."
                   </td>
               </tr>
                <tr>
                   <th> City</th>
                   <td>".$datacp['city']."</td>
               </tr>";

               echo $datac;


}
// get brands
public function get_brand(Request $request)
{
  $ids = BrandAsignroles::where('customer_id',auth()->user()->id)->whereIn('role',[1,5])->where('status',1)->where('type',null)->pluck('brand_campaign_id')->toArray();
  
$data = Brand::whereIn('id',$ids)->where(function($q) use ($request){
  if(!empty($request['search'])){
return $q->where('name','like',"%{$request['search']}%");
  }
})->with('countryx')->with('advertiser')->orderBy($_GET['orderby'], $_GET['ascdesc'])->paginate($_GET['pagination'])->toArray();

$output = '';
foreach ($data['data']  as $key => $value) {
  $roles= BrandAsignroles::where('brand_campaign_id',$value['id'])->where('status',1)->where('type',null)->first();
if($roles['role']==5){
$role = 'Brand Owner';
}elseif($roles['role']==1){
  $role = 'Brand Manager';
}


if(!empty($value['status'])){
$status = '<a class="order_status">Active</a>';
}else{
  $status = '<a class="expired_status">Deactive</a>';
}
$edit = url('customer/updatebrand',$value['id']);

$output .='<tr><td style="display:none;"></td>
<td>'.$role.'</td>
<td>'.$value['name'].'</td>
<td><img src="'.url('brandlogos',$value['logo']).'" height="100" width="100"/></td>
<td>'.$value['countryx']['manage_county'].'</td>
<td>'.$value['advertiser']['name'].'</td>
<td>'.$status.'</td>
<td><a href="'.$edit.'"><i class="bx bx-edit"></i></a></td>
</tr>';

}

if($data['current_page']==1){
  $pb = 'disabled';
}else{
  $pb = '';
}
if($data['current_page']==$data['last_page']){
  $pbl = 'disabled';
}else{
  $pbl = '';
}

$prev = $data['current_page']-1;
$nxt = $data['current_page']+1;

$page = '<ul class="pagination">
<li><a  class="gotopage btn" page="'.$prev.'" '. $pb.'> < </a></li>';

for ($i=1; $i <=$data['last_page'] ; $i++) {
    if( $i>=$data['current_page']-5 && $i<=$data['current_page']+5){
  if($data['current_page']==$i){ 
  $page .= '<li><a class="gotopage btn btn-primary" page="'.$i.'">'.$i.'</a></li>';
  }else{
    $page .= '<li><a  class="gotopage btn" page="'.$i.'">'.$i.'</a></li>';
  }
}
}
if($data['current_page']==$data['last_page']){ 
$page .= '<li><a  class="gotopage btn" page="'.$data['last_page'].'" '. $pbl.'> > </a></li></ul>';
}else{
  $page .= '<li><a  class="gotopage btn" page="'.$nxt.'" '. $pbl.'> > </a></li></ul>'; 
}

return json_encode(['data'=>$output,'page'=>$page]);

}


public function brand_campaigns(Request $request)
{
  return view('customer.brand.campaign.campaign');
  
}

public function brand_add_campaign(Request $request)
{
  if(!empty($_GET['offer'])){
    $data  = offer::with('Property_Type')->with('advertiser')->with('formatx')->with('Brand')->with('ordersx')->with('typeformate')->with('price_type')->where('offer_name',$_GET['offer'])->get();
  }else{
    $data = [];
  }

  return view('customer.brand.campaign.add_campaign',['data'=>  $data ]);
}

// validate campaign
public function brand_camp_validate(Request $request)
{
  if($request['tab']==1){
    $validator = Validator::make($request->all(), [
      'campaign_name' => 'required|max:255',
      'campaign_brand' => 'required|max:255',
      'campaign_from' => 'required|date|after_or_equal:today',
      'campaign_to' => 'required|date|after_or_equal:campaign_from',
      'campaign_private' => 'required',
      'campaign_deadline' => 'required|date|after_or_equal:campaign_to',
      'campaign_description' => 'required|min:50',
      'campaign_note' => 'required|min:50',
      'campaign_payable' => 'required',
      'campaign_payable_date' => 'required|date|after_or_equal:campaign_from',
  ]);
   
  if ($validator->fails()) {
       Session::flash('error', $validator->messages()->first());
       return $validator->messages();
  }else{
    session()->put('camp',$request->all());
    return 200;
  }
   
  
    }


    if($request['tab']==2){
      $validator = Validator::make($request->all(), [
        'flight.*.flight_name.*' => 'required|max:255',
        'flight.*.select_site.*' => 'required|max:255',
        'flight.*.type_of_formate.*' => 'required|max:255',
        'flight.*.formate.*' => 'required|max:255',
        'flight.*.price_type.*' => 'required|max:255',
        'flight.*.from.*' => 'required|date|after_or_equal:today',
        'flight.*.to.*' => 'required|date|after_or_equal:flight.*.from.*',
        'flight.*.target_country.*' => 'required|max:255',
        'flight.*.bprice.*' => 'required|max:255',
        'flight.*.target_sport.*' => 'required|max:255',
        'flight.*.kpi.*' => 'required|max:255',
        'flight.*.description.*' => 'required|max:255',
        'flight.*.AWeight.*' => 'required|max:100',
        'flight.*.QWeight.*' => 'required|max:100',
        'flight.*.deasline_flight.*' => 'required|max:255',

    ]);
     
    if ($validator->fails()) {
         Session::flash('error', $validator->messages()->first());
         return $validator->messages();
    }else{
      session()->put('flight',$request->all());
      return 200;
    }
     
    
      }


}

// set flights result
public function get_flights(Request $request)
{

 
    
     $camp = session()->get('camp');
     $flights =session()->get('flight');
  
     if(!empty($flights['offer'])){
      $offer_update =offer::where('offer_name',$flights['offer'])->pluck('property_id')->toArray();
       }else{
        $offer_update =[];
       }

    $adver =  Brand::where('id', $camp['campaign_brand'])->with('advertiser')->first();
    $Advertiser = $adver['advertiser']['id'];
    $Brand = $camp['campaign_brand'];
    $from = $camp['campaign_from'];
    $to = $camp['campaign_to'];
    $Description = $camp['campaign_description'];
    $notes  =$camp['campaign_note'];
    $pp = $camp['campaign_private'];
    
    $basic_values = ['Advertiser'=>$Advertiser,'Brand'=>$Brand,'from'=>$from,'to'=>$to,'Description'=>$Description,'notes'=>$notes,'pp'=>$pp,];
    $output = [];
    $no=1; 
    foreach ($flights['flight'] as $key => $value) {
    
    $dat = my_formate::where('type_of_formate',$value['type_of_formate'][0])->where('formate',$value['formate'][0])->get();
      
    
    
    $id = [];
    
    
    foreach ($dat as $keyn => $ids) {
    
      $id[$keyn] = $ids->partner_properties_id;
    }
 
    if(!empty($id[0])){
     $id =$id;
    }else{
      $id =[];
    }
    
      $data = Partner_property::with('propertytype')->with('country')->where(function($ret) use ($id){
    
        return $ret->whereIn('id',$id);
     })->where('select_site',$value['select_site'][0])->where('target_country','LIKE','%'.$value['target_country'][0].'%')->where(
    function ($xdata) use ($value)
    {
     if(!empty($value['target_sport'])){
      for ($i=0; $i < count($value['target_sport']); $i++) { 
        $xdata->orWhere('target_sport','LIKE','%'.$value['target_sport'][$i].'%');
      } 
      return  $xdata;
     }
    }
    
      )->where(function($queryc) use ($value){
    if(!empty($_GET['ready']) && !empty($value['property_id'])){
    
     return  $queryc->whereIn('id',$value['property_id']);
    }
    
      })->where('active',0)->get();
      
    
    
    
    if(count($data)>0){
    
    foreach ($data as $kez => $val) {
      // price calculations
      $val['pcq'] = !empty(intval($val['pcq'])) ? $val['pcq'] : 100;
      $val['pa'] = !empty(intval($val['pa'])) ? $val['pa'] : 100;

    if(empty($value['QWeight'][0]) && empty($value['AWeight'][0])){
    $val['pricex']  = str_replace(',','.',$value['bprice'][0]);
    }else{
      intval($value['QWeight'][0]);
      intval($value['AWeight'][0]);
    $price =787;
    // ($value['QWeight'][0]/($value['QWeight'][0]+$value['AWeight'][0]))*$val['pcq']*str_replace(',','.',$value['bprice'][0])+($value['AWeight'][0]/($value['QWeight'][0]+$value['AWeight'][0]))*$val['pa']*str_replace(',','.',$value['bprice'][0]) ;
    

      $val['pricex'] = $price/100;
    }
    $dataxz[$key][$kez] = $val;
    
    
    }
    

    
    $vvvnoo = $no;
    // table html
    
    $output[$no] = '';

    foreach ($dataxz[$key] as $keycv => $valuec) {
      // dd($value['type_of_formate'][0]);
    $datan = Customer::where('id',$valuec->customer_id)->first();
      if(!empty($datan)){
    
        $descriptiont = str_replace('"','**',$value['description'][0]);
    if(in_array($valuec->id,$offer_update)){
      $cheked = 'checked';
      $bgcolor = '#f5f5f5';
      $color = '#000';
    }else{
      $cheked ='';
      $bgcolor = '#f5f5f5';
      $color = '#000';
    }
  

     $output[$no] .= '
     <div class="col-md-4 pt-2" style=""> 
     <div class="card" style="overflow:auto;background:'.$bgcolor.';color:'.$color.';">
     <table class="table ">
         <tr>  <td colspan="2"><input
         '.$cheked.' type="checkbox"
      payable="'.$camp['campaign_payable'].'"
      payable_date="'.$camp['campaign_payable_date'].'"
      deadline="'.$value['deasline_flight'][0].'"
      c_deadline="'.$camp['campaign_deadline'].'" 
      class="flipswitchm checkv" flight_name="'.$value['flight_name'][0].'" 
      campaign_name="'.$camp['campaign_name'].'" 
      target_sport="'.implode(',',$value['target_sport']).'" 
      kpi="'.implode(',',$value['kpi']).'"
      price_type="'.$value['price_type'][0].'"
      qweight="'.$value['QWeight'][0].'" 
      aweight="'.$value['AWeight'][0].'" 
      property_type="'.$valuec->select_site.'"
      format_type="'.$value['type_of_formate'][0].'" 
      description="'.$descriptiont.'" 
      country='.$value['target_country'][0].'
      flight="'.$vvvnoo.'" 
      from="'.$value['from'][0].'"
      to="'.$value['to'][0].'"
      bprice="'.$value['bprice'][0].'"
      title="'.$valuec->customer_id.'" 
      formate="'.$value['formate'][0].'"
      id="'.$valuec->id.'"
      value="'.implode(',',$basic_values).'" 
      price="'.$valuec->pricex.'" '.$cheked.'              
           />&nbsp;
             <span></span></td></tr>
             <tr><th>Flight</th>  <td>'.$no.'</td></tr>
             <tr><th>Property</th>  <td>'.$valuec->name.'</td></tr>
         <tr><th>Type</th>  <td>'.$valuec->propertytype->name.'</td></tr>
         <tr><th>Partner</th>  <td>'.$datan['first_name'].'</td></tr>
         <tr><th>Url</th>  <td>'.$valuec->url.'</td></tr>
         <tr><th>Price</th>  <td>'.number_format($valuec->pricex, 2, ',', '.').' €</td></tr>
     </table>
     </div>   </div>
     ';

 
      }else{
        $output[$no] .= '<div class="col-md-12"><p class="text-center">Result not found<p></div>'; 
      }
    }
   
    // html end

    }else{
     
      $output[$no] = '<div class="col-md-12"><p class="text-center">Result not found<p></div>'; 
    
    
    } //if !empty
    
    $no++;
    } //end mega foreach 
       
    
    return json_encode($output);
    


}


 ##########################################
   // media type data for select field
                        public function getMediatype(Request $request)
                        {

                        $products= $this->manage_social_media::where('status',0)->where(function($q) use($request) {
                        if(!empty($request->q)){
                        return $q->where('name', 'LIKE', "%$request->q%");
                        }
                        });
                        return $products->paginate(7, ['*'], 'page', $request->page)->toArray();

                        }
  // format type data for select field
                        public function getformateype(Request $request)
                        {

                        $idds =  $this->formate::where('type_of_properties',$request->media_type)->pluck('type_of_formate')->toArray();

                        $products= $this->typeformate::whereIn('id',$idds)->where('status',0)->where(function($q) use($request) {
                        if(!empty($request->q)){
                        return $q->where('name', 'LIKE', "%$request->q%");
                        }
                        });
                        return $products->paginate(7, ['*'], 'page', $request->page)->toArray();

                        }
  // format data for select field
                        public function getformat(Request $request)
                        {
                        $products= $this->formate::where('type_of_properties',$request['f_media_type'])->where('type_of_formate',$request->format_type)->where('status',0)->where(function($q) use($request) {
                        if(!empty($request->q)){
                        return $q->where('formate', 'LIKE', "%$request->q%");
                        }
                        });
                        return $products->paginate(7, ['*'], 'page', $request->page)->toArray();
                        }

 // pricetype data for select field
                        public function getpricetype(Request $request)
                        {
                        $products= $this->price_type::where('namea',$request->format_type)->where('status',0)->where(function($q) use($request) {
                        if(!empty($request->q)){
                        return $q->where('name', 'LIKE', "%$request->q%");
                        }
                        });
                        return $products->paginate(7, ['*'], 'page', $request->page)->toArray();
                        }

  // country data for select field
                        public function gettarget_country(Request $request)
                        {
                        $products=   $this->manage_county::where('status',0)->where(function($q) use($request) {
                        if(!empty($request->q)){
                        return $q->where('manage_county', 'LIKE', "%$request->q%");
                        }
                        });
                        return $products->paginate(7, ['*'], 'page', $request->page)->toArray();
                        }
  // target  sport  data for select field
                        public function gettarget_sport(Request $request)
                        {
                        $products=   $this->Target_Sport::where('status',0)->where(function($q) use($request) {
                        if(!empty($request->q)){
                        return $q->where('name', 'LIKE', "%$request->q%");
                        }
                        });
                        return $products->paginate(7, ['*'], 'page', $request->page)->toArray();
                        }

                        // kpi data for select field
                        public function getkpi_data(Request $request)
                        {
                        $products= kpi::where('status',0)->where(function($q) use($request) {
                        if(!empty($request->q)){
                        return $q->where('name', 'LIKE', "%$request->q%");
                        }
                        });
                        return $products->paginate(7, ['*'], 'page', $request->page)->toArray();
                        }





// send offer to media //
public function sendoffer(Request $request)
{

$encode_id = null;
$encofe  = offer::where('offer_name',$request['offername'])->first();
if(isset($encofe) && !empty($encofe)  && $encofe!=='null'){
  $dd['ad_serv_camp'] = null;
  $encode_id  =  $encofe['encode_camp'];
   offer::where('offer_name',$request['offername'])->delete();

}else{
 
  $dd['ad_serv_camp'] = null;
}

  foreach ($request['data'] as $key => $value) {

    $data = $value;
    $url =url('/customer/Notifications');

    if($request->status!='222219' || $request->status!=222219){

    $datam = Customer::where('email',$value[1])->get();

    if ($datam[0]['lang']=='en') {
      $emailtemp = 'App\Model\amail_template_manage';
       }else{
         $emailtemp = 'App\Model\amail_template_manages_ita';
       }
       $temps =  $emailtemp::where('id',12)->get();

  $temp = str_replace(["&#39;","&gt;","&amp;&amp;"],["'",">","&&"],$temps[0]['content']);
  
  
  $path = resource_path().'/views/email_temp/email_temp.blade.php';
  
  $fp = fopen($path, 'w');
  fwrite($fp, $temp);
  fclose($fp);
$datax =   Partner_property::where('id',$data[0])->get();
$name = Customer::where('email',$value[1])->get();
$brand = Brand::all();
$pricetype =   $this->manage_social_media::all();
$advertiser = Advertiser::all();
$v = DB::table('mail')->first();

// if($datam[0]['notification']==0){
//   Mail::send('email_temp.email_temp', ['data'=>$data,'url'=>$url,'datax'=>$datax,'name'=>$name,'brand'=>$brand,'pricetype'=>$pricetype,'advertiser'=>$advertiser], function ($m) use ($value,$v,$temps) {
//         $m->from($v->mail);
//            $m->to($value[1])->subject($temps[0]['name']);
//         });
//       }
    } 
      
      
$exp = explode(',',$value[2]);

    $lastoid =       offer::create([
            'Advertiser'=> $this->RemoveSpecialChar($exp[0]),
            'from'=>$value[7],
            'to'=>$value[8],
            'pp'=> $this->RemoveSpecialChar($exp[6]),
            'internal_note'=> $this->RemoveSpecialChar($exp[5]),
            'description'=> $this->RemoveSpecialChar($exp[4]),
            'brand'=> $this->RemoveSpecialChar($exp[1]),
            'offer_name'=> $this->RemoveSpecialChar($request['offername']),
            'email'=> $this->RemoveSpecialChar($value[1]),
            'property_id'=> $this->RemoveSpecialChar($value[0]),
            'price'=>$value[10]==21 ? $value[3] : null,
            'send_price'=> $value[3],
            'format'=> $this->RemoveSpecialChar($value[4]),
            'flight'=> $this->RemoveSpecialChar($value[5]),
            'bprice'=> $value[6],
            'property_type'=> $this->RemoveSpecialChar($value[9]),
            'formate_type'=> $this->RemoveSpecialChar($value[10]),
            'flight_description'=> str_replace('**','"',$value[11]),
            'country'=> $this->RemoveSpecialChar($value[12]),
            'target_sport'=> $value[13],
            'pricetype'=> $this->RemoveSpecialChar($value[14]),
            'qweight'=> $this->RemoveSpecialChar($value[15]),
            'aweight'=> $this->RemoveSpecialChar($value[16]),
            'Campaign_name'=> $this->RemoveSpecialChar($value[17]),
            'flight_name'=> $this->RemoveSpecialChar($value[18]),
            'kpi'=> $value[19],
            'cfom'=>$exp[2],
            'cto'=>$exp[3],
            'deadline'=>$value[20],
            'c_deadline'=>$value[21],
            'statusx'=>$request->status,
            'statusxy'=>$request->status,
            'payable'=>$value[22]=='yes' ? 1 : 0,
            'payable_date'=>empty($value[23]) ? null : $value[23],
            'partnership'=>$value[10]==21 || $value[10]=='21' ? 1 : 0,
            'ad_serv_camp'=> $dd['ad_serv_camp'],
            'lat_status'=>$request['lat_status'],
            
          ]);


   
  }

$dattap = offer::where('offer_name',$lastoid->offer_name)->get();
if(!empty($encode_id)){
offer::where('offer_name',$lastoid->offer_name)->update(['encode_camp'=>$encode_id]);
}else{
  offer::where('offer_name',$lastoid->offer_name)->update(['encode_camp'=>$this->numhash($lastoid->id)]);
}

foreach($dattap as $rws){

  offer::where('id',$rws->id)->update(['encode'=>$this->numhash($rws->id)]);

}

  if($request->status==222219){


    if($_POST['name']==null || $_POST['name']=='null'){
      return json_encode(['success'=>'Campaign Saved Successfully.']);
    }else{
      return json_encode(['success'=>'Campaign Updated Successfully.']);
    
    }
  
  }else{
    return json_encode(['success'=>'Invitation Sent Successfully.']);

  }
 

}

// hash numhas convert number
function numhash($n) {
  return (((0x0000FFFF & $n) << 12) + ((0xFFFF0000 & $n) >> 12));
}

}
