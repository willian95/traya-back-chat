<style media="screen">
.alert {
min-width: 150px;
padding: 15px;
margin-bottom: 20px;
border: 1px solid transparent;
border-radius: 3px;
}
.alert-success {
    background-color: #91cf91;
    border-color: #80c780;
    color: #3d8b3d;
    width: 50%;
    position: relative;
    left: 26%;
}
.alert-warning {
background-color: #ebc063;
border-color: #e8b64c;
color: #a07415;
}
.alert-danger {
background-color: #e27c79;
border-color: #dd6864;
color: #9f2723;
width: 50%;
position: relative;
left: 26%;
}
.alert p {
padding: 0;
margin: 0;
}
.alert i {
padding-right: 5px;
vertical-align: middle;
font-size: 24px;
}
.alert .close-alert {
-webkit-appearance: none;
position: relative;
float: right;
padding: 0;
border: 0;
cursor: pointer;
color: inherit;
background: 0 0;
font-size: 21px;
line-height: 1;
font-weight: bold;
text-shadow: 0 1px 0 rgba(255, 255, 255, 0.7);
filter: alpha(opacity=40);
opacity: .4;
}
.alert .close-alert:hover {
filter: alpha(opacity=70);
opacity: .7;
}

.shadow-1, .alert {
box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
}

.shadow-2, .alert:hover {
box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23);
}

</style>

@if(Session::has('error'))
<div class="alert alert-danger" role="alert">
  <button type="button" class="close-alert">×</button>
  <p>  {{Session::get('error')}}</p>
</div>
@endif
@if(isset($errors) && count($errors)>0)
<div class="alert alert-danger" role="alert">
  <button type="button" class="close-alert">×</button>
  <ul>
    @foreach($errors as $error)
    <li>{{$error[0]}}</li>
    @endforeach
  </ul>
</div>
@endif
