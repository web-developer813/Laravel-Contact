<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use  Input, Redirect, Session, Validator, DB, Mail,File, Request,Response,URL,Form;
use App\Model\AdminUser as AdminUserModel;
class ProfileController extends Controller{
    public function __construct() {
        $this->beforeFilter(function(){
            if (!Session::has('admin_id')) {
                return Redirect::route('admin.auth.login');
            }
        });
    }

    public function index() {
        $param['pageNo'] = 111;
        $id = Session::get('admin_id');
        if ($alert = Session::get('alert')) {
            $param['alert'] = $alert;
        }
        $param['admin'] = AdminUserModel::find($id);
        return View::make('admin.dashboard.profile')->with($param);
    }
    public function store(){
        $rules = [  'currentPassword'  => 'required',
                    'newPassword'  => 'required',
                    'confirmNewPassword' => 'required',
        ];
        $validator = Validator::make(Input::all(), $rules);

        if ($validator->fails()) {
            return Redirect::back()
                ->withErrors($validator)
                ->withInput();
        }else{
            $id = Session::get('admin_id');
            $currentPassword= Input::get('currentPassword');
            $newPassword = Input::get('newPassword');
            $confirmNewPassword = Input::get('confirmNewPassword');
            if($newPassword != $confirmNewPassword) {
                $alert['msg'] = 'Please check again your New Password and Confirm New Password';
                $alert['type'] = 'danger';
            }else{
                $userList = AdminUserModel::whereRaw('AdminUserPassword = md5(?)', array($currentPassword))->get();
                if(count($userList) >0) {
                    $user = AdminUserModel::find($id);
                    $user->AdminUserPassword=md5($newPassword);
                    $user->save();
                    $alert['msg'] = 'User has been updated successfully';
                    $alert['type'] = 'success';
                }else{
                    $alert['msg'] = 'Your Current Password is incorrect.';
                    $alert['type'] = 'danger';
                }
            }
        }
        return Redirect::route('admin.profile')->with('alert', $alert);
    }
}