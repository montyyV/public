<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Partner_property;
use App\Model\offer;
use App\Model\orders;
class Asignroles extends Model
{
  protected $table ='asign_roles';
  protected $fillable = [
       'main_owner_id','owner_id','customer_id','media_order_id','status','role','permissions','type','check_if_order'
    ];


    public function media()
   {
       return $this->hasOne('App\Partner_property','id','media_order_id');
   }

   /*get media id using order id*/
   public static function getmedia_id_by_order_id($id){
    $data  = orders::where('id',$id)->first();
    $off = offer::where('id',$data['offer_id'])->first();

    return $off['property_id'];
   }
   

   public static function get_permission_by_media_id($user_id,$media_id,$role,$status,$permision,$type){
 
  $asign_roles=Asignroles::where(function($qu) use($type){
   return  $qu->where('type',$type);
  
  })->where('media_order_id',$media_id)->where('customer_id',$user_id)->
  
  whereIn('role',$role)->where('status',$status)->first();
  if(!empty($asign_roles)){
  if($asign_roles['role']==1 ){

    $check = \DB::table('sections')->where('id',$permision)->where('manager',1)->count();

  }elseif ($asign_roles['role']==2 ) {

    $check = \DB::table('sections')->where('id',$permision)->where('custom',1)->count();

  }elseif ($asign_roles['role']==3 ) {

    $check = \DB::table('sections')->where('id',$permision)->where('contributor',1)->count();

  }elseif ($asign_roles['role']==5 ) {

    $check = \DB::table('sections')->where('id',$permision)->where('owner',1)->count();
   
  }
}else{
  $check ='';
}

if( !empty($check)){
   return   $asign_roles;
}
}



     public static function getAsign_roles_media_ids($user_id){
  
      $media_ids=[];
     $asign_roles=Asignroles::where('customer_id',$user_id)->whereIn('role',[1,5,2])->get();
     if(!empty($asign_roles)){
      foreach($asign_roles as $item){
        if($item->role==1){
          $media_ids[]=$item->media_order_id;
        }
      }
     }
     return $media_ids;
 }

   
   /*get media ids of manger asigned medias */
   public static function getAsign_roles_mangager_media_ids($user_id){
  
        $media_ids=[];
       $asign_roles=Asignroles::where('customer_id',$user_id)->where('status',1)->get();
       if(!empty($asign_roles)){
        foreach($asign_roles as $item){
          if($item->role==1 || $item->role==2){
            $media_ids[]=$item->media_order_id;
          }
        }
       }
       return $media_ids;
   }
   /*get  contributor  asigned order */
   public static function getAsign_roles_contributor_order_ids($user_id){
        $order_ids=[];
       $asign_roles=Asignroles::where('customer_id',$user_id)->where('status',1)->get();
               if(!empty($asign_roles)){
                foreach($asign_roles as $item){
                   if($item->role==3){
                    //contributor order_ids
                    $order_ids[]=$item->media_order_id;
                   }
               }
           }
       return $order_ids;
   }
}

