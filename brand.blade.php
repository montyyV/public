@extends('customer.include.main')

@section('content')
<style>
.notificatio {

  position: relative;
 
}
.gotopage{
    border: 1px solid #8080803b;
  }
td{
    padding:5px!important;
}

.notificatio .badge {

  padding: 3px 5px;
  border-radius: 50%;
  border-color: #ff2829 !important;
  color: white;
}
.table thead th {
    font-size: 14px;
    letter-spacing: 0px;
    padding: 10px;
    background: #f5f5f5;
}
.table th, .table td {
    vertical-align: middle !important;
    border: 1px solid #e9e9e9 !important;
}
table tr td label {
    text-transform: capitalize !important;
    font-weight: 400 !important;
}
.iconss-customm a.mb-1[title="View Invitations"] {
    margin-bottom: 0 !important;
    display: inline-block;
    vertical-align: middle;
    margin-right: 10px;
}
input.megasearch[type=search], select.form-control.paginationn {
    border: 1px solid #dbdbdb !important;
    border-radius: 0.25rem !important;
}
.pagesss ul.pagination li a {
    cursor: pointer;
}
@media (min-width: 601px) {
.dropp-boxx {
    width: 160px;
    flex: 0 0 auto;
    max-width: 100%;
}
.search-boxx {
    flex: 0 0 auto;
    max-width: 100%;
    width: calc(100% - 160px);
}
}
    </style>
<div class="app-content content" style=" background:#727E8C24!important;">
        <div class="content-overlay"></div>
        <div class="content-wrapper">
        <div class="content-header row w-100 pl-1">
                <div class="content-header-left col-12 mb-2">
                    <div class="row breadcrumbs-top position-static" style="background: #ebedef;">
                        <div class="col-12">
                            <h5 class="content-header-title float-left pr-1 mb-0">{{trans('messages.brand')}} </h5>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb p-0 mb-0" style=" background:#727e8c00!important;">
                                    <li class="breadcrumb-item"><a href="{{url('customer/dashboard/')}}"><i class="bx bx-home-alt"></i></a>
                                    </li>
                                   

                                    <li class="breadcrumb-item active">{{trans('messages.brand')}}   
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<div class="">

<div class="col-md-12">
<center>
<input type="hidden" value="{{trans('messages.Negotiation')}}" class="head_t" /> 
<!-- <h3><b>{{trans('messages.brand')}}<b> </h3> -->
@if(session('success'))
<div class="alert alert-success" style="background:#05CF6A!important;font-weight:600;">
{{session('success')}}
</div>
@endif
</center>


             
                   
<section id="">
                    <div class="row">
                        <div class="col-12">
                            <div class="card rounded">
                                <div class="">
                            
<a class="btn btn-primary " style="float: right" href="{{url('customer/addbrand')}}">Add Brand</a>
<?php  
function numhash($n) {
    return (((0x0000FFFF & $n) << 12) + ((0xFFFF0000 & $n) >> 12));
}
?>
<div class="">

<div class="row mx-0 w-100 mb-1 justify-content-end">
<div class="col-md-12 selctboxx"></div>
<div class="col-md-4 search-boxx">
  <input type="search" placeholder="Search" class="form-control megasearch"/>
</div>
<div class="col-md-auto pt-1 dropp-boxx">
  <select class="form-control paginationn" style="min-width: 120px;">
  <option value="20">20</option>
  <option value="40">40</option>
  <option value="60">60</option>
  <option value="80">80</option>
  </select>
</div>


</div>


<table width="100%"class="table   table-responsive text-center px-1" >
    <thead>
<tr>
<th style="display:none;"></th>
<th>&nbsp;Role</th>
<th width="10%" class="text-center asc" title="brand">{{trans('messages.Brand')}}</th>

<th width="20%" class="text-center asc" title="Campaign_name">{{trans('messages.logo')}}</th>

<th width="10%">{{trans('messages.Country')}}</th>
<th width="10%">{{trans('messages.Advertiser')}}</th>
<th width="10%" class="">{{trans('messages.Status')}}</th>
<th width="10%" class="hideshorting">{{trans('messages.Action')}}
</th>

</tr>
</thead>
<tbody id="bbbody" class="iconss-customm">

</tbody>
</table>
<div class="pagesss px-1 d-flex justify-content-end flex-wrap"> </div>


</div>
</div>
</div>

</div>
</div>
</div>
</section
</div>
</div>


</div>
</div>
<style>
label{
    text-transform: none!important;
}
</style>




<script>
pagination = 20;
var statusd='' ;
var search='';
var orderby='';
var ascdesc = '';
var page = 1;

fetch_invitations(pagination,status,statusd,search,orderby='id',page,ascdesc='ASC');

function fetch_invitations(pagination,status,statusd,search,orderby,page,ascdesc){
$.ajax({
  url:"{{url('customer/get_brand')}}?page="+page+status,
  method:'get',
  data:{statusd:statusd,search:search,orderby:orderby,pagination:pagination,ascdesc:ascdesc},
dataType:'json',
success:function(resp){
  $('#bbbody').html(resp.data);
$('.pagesss').html(resp.page);
}
});
}

$(document).on('click','.gotopage',function(){
  padd = $(this).attr('page');
  fetch_invitations(pagination,status,statusd,search,orderby,page=padd,ascdesc);
});


$(document).on('change','.select_status',function(){
if(this.value==9){
  sts = "&Partnership="+this.value;
}else{
sts = "&status="+this.value;
}
fetch_invitations(pagination,status=sts,statusd,search,orderby,page,ascdesc);
});

$(document).on('keyup','.megasearch',function(){
  serc = this.value;
  fetch_invitations(pagination,status,statusd,search=serc,orderby,page,ascdesc);
});

$(document).on('change','.paginationn',function(){
  ppp = this.value;
  fetch_invitations(pagination=ppp,status,statusd,search,orderby,page,ascdesc);
});


$(document).on('click','.desc',function(){
  ppp = this.title;
    $(this).removeClass('desc');
  $(this).addClass('asc');
  fetch_invitations(pagination,status,statusd,search,orderby=ppp,page,ascdesc='DESC');
});

$(document).on('click','.asc',function(){
  ppp = this.title;
  $(this).removeClass('asc');
  $(this).addClass('desc');
  fetch_invitations(pagination,status,statusd,search,orderby=ppp,page,ascdesc='ASC');
});


</script>
<style>
.asc::before {
    content: ' \02C4';
}

.desc::before {
    content: ' \02C5';
}
</style>

@endsection