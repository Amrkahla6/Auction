<?php

namespace App\Http\Controllers\API;

use Validator;
use App\Models\Member;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\API\BaseController as BaseController;

class UserController extends BaseController
{
    //Commercial registration
    public function registerBusiness(Request $request)
    {
        try {
            // return $request;
            $validator = Validator::make(
                $request->all(),
                [
                    'commercial_record'   => 'required|unique:members',
                    'phone'               => 'required|unique:members',
                    'date_of_birth'       => 'required',
                    'id_number'           => 'required|unique:members',
                    'email'               => 'required|unique:members',
                    'password'            => 'required|min:6',
                ],
                [
                    'commercial_record.required'   => __("user.commercial_record"),
                    'commercial_record.unique'     => __("user.commercial_exist"),
                    'phone.required'               => __("user.phone"),
                    'phone.unique'                 => __("user.unique_phone"),
                    'date_of_birth.required'       => __("user.date_of_birth"),
                    'id_number.required'           => __("user.id_number"),
                    'id_number.unique'             => __("user.unique_id_number"),
                    'email.required'               => __("user.email"),
                    'email.unique'                 => __("user.unique_email"),
                    'password.required'            => __("user.password"),
                    'password.min'                 => __("user.max_password"),
                ]
            );

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            $newmember                     = new Member;
            $newmember->commercial_record  = $request['commercial_record'];
            $newmember->phone              = $request['phone'];
            $newmember->date_of_birth      = $request['date_of_birth'];
            $newmember->id_number          = $request['id_number'];
            $newmember->email              = $request['email'];
            $newmember->password           = Hash::make($request['password']);
            $newmember->save();
            $message = __("user.regist_success");
            return $this->returnData('user', $message);
        }catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }



    //registeration process
    public function register(Request $request)
    {
        try {
            // return $request;
            $validator = Validator::make(
                $request->all(),
                [
                    'username'            => 'required',
                    'phone'               => 'required|unique:members',
                    'date_of_birth'       => 'required',
                    'nationality'         => 'required',
                    'email'               => 'required|unique:members',
                    'password'            => 'required|min:6',
                ],
                [
                    'username.required'            => __("user.username"),
                    'phone.required'               => __("user.phone"),
                    'phone.unique'                 => __("user.unique_phone"),
                    'date_of_birth.required'       => __("user.date_of_birth"),
                    'nationality.required'         => __("user.nationality"),
                    'email.required'               => __("user.email"),
                    'email.unique'                 => __("user.unique_email"),
                    'password.required'            => __("user.password"),
                    'password.min'                 => __("user.max_password"),
                ]
            );

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            $newmember                     = new Member;
            $newmember->username           = $request['username'];
            $newmember->phone              = $request['phone'];
            $newmember->date_of_birth      = $request['date_of_birth'];
            $newmember->nationality        = $request['nationality'];
            $newmember->email              = $request['email'];
            $newmember->password           = Hash::make($request['password']);
            $newmember->save();
            $message = __("user.regist_success");
            return $this->returnData('user', $message);
        }catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }


    /**
     * Member Login
     */

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email'          => 'required',
                'password'       => 'required',
            ], [
                'email.required'        => __("user.email"),
                'password.required'     => __("user.password"),
            ]);

                if ($validator->fails()) {
                    $code = $this->returnCodeAccordingToInput($validator);
                    return $this->returnValidationError($code, $validator);
                }
                //login

                $credentials = $request->only(['email', 'password']);
                $token = Auth::guard('member-api')->attempt($credentials);  //generate token

                if (!$token)
                    return $this->returnError('E001', __('user.false_info'));

                $user = Auth::guard('member-api')->user();
                $user ->api_token = $token;

                $response = [
                    'id'        => $user->id,
                    'username'  => $user->username,
                    'phone'     => $user->phone,
                    'email'     => $user->email,
                    'api_token' => $token,
                ];
                //return token
                return $this->returnData('user', $response,__('user.login'));  //return json response
        } catch (\Exception $ex) {
                return $this->returnError($ex->getCode(), $ex->getMessage());
            }
    }

    /**
     * Member Logout
     */

    public function logout(Request $request)
    {
         $token = $request->header('auth-token');
        if($token){
            try {
                JWTAuth::setToken($token)->invalidate(); //logout
            }catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
                return  $this -> returnError('', __('user.wrangs'));
            }
            return $this->returnSuccessMessage(__('user.logout'));
        }else{
            return $this -> returnError('',__('user.wrangs'));
        }
    }


 /**
  * Member forgetpassword process
  */
 public function forgetpassword(Request $request)
 {
     $user = Member::where('email', $request->email)->first();
     if (!$user) {
         $errormessage = __('user.wrang_email');
         return $this -> returnError('',$errormessage);
    } else {
         $randomcode        = substr(str_shuffle("0123456789"), 0, 4);
         $user->forgetcode  = $randomcode;
         $user->save();
         $successmessage = __('user.sent_email');
         return $this->returnData('success', $user->forgetcode, $successmessage);
     }
 }

 /**
  * Member Active code for forget password
  */

  public function activcode(Request $request)
  {
      $user = Member::where('email', $request->email)->where('forgetcode', $request->forgetcode)->first();
      if ($user) {
          return $this->returnData('success', "true",);
      } else {
          $errormessage = __('user.wrang_code');
          return $this -> returnError('',$errormessage);
      }
  }

  /**
   * Member Chanage Password
   */
  public function rechangepass(Request $request)
  {
      $validator = Validator::make(
          $request->all(),
          [
              'new_password'    => 'required',
          ]
      );

      if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }

      $member = Member::where('email', $request->email)->first();
      if ($member) {
          $member->password = Hash::make($request->new_password);
          $member->save();
          $errormessage = __('user.new_pass');
          return $this->returnData('success', $errormessage);
      } else {
          $errormessage = __('user.wrang_email');
          return $this -> returnError('error',$errormessage);
      }
  }

    /**
     *  updating profile Commercial registration
     */

    public function updateCommercialProfile(Request $request)
    {
        $upmember = Member::where('id', $request->member_id)->first();
        if ($upmember) {
            if($upmember->commercial_record){
                $validator = Validator::make($request->all(), [
                    'commercial_record'   => 'required|unique:members,commercial_record,' . $upmember->id,
                    'phone'               => 'required|unique:members,phone,' . $upmember->id,
                    'date_of_birth'       => 'required',
                    'id_number'           => 'required|unique:members,id_number,' . $upmember->id,
                    'email'               => 'required|unique:members,email,' . $upmember->id,
                    'password'            => 'required|min:6',
                ],
                [
                    'commercial_record.required'   => __("user.commercial_record"),
                    'commercial_record.unique'     => __("user.commercial_exist"),
                    'phone.required'               => __("user.phone"),
                    'phone.unique'                 => __("user.unique_phone"),
                    'date_of_birth.required'       => __("user.date_of_birth"),
                    'id_number.required'           => __("user.id_number"),
                    'id_number.unique'             => __("user.unique_id_number"),
                    'email.required'               => __("user.email"),
                    'email.unique'                 => __("user.unique_email"),
                    'password.required'            => __("user.password"),
                    'password.min'                 => __("user.max_password"),
                ]);
                if ($validator->fails()) {
                    $code = $this->returnCodeAccordingToInput($validator);
                    return $this->returnValidationError($code, $validator);
                }
                $upmember->commercial_record  = $request['commercial_record'];
                $upmember->phone              = $request['phone'];
                $upmember->date_of_birth      = $request['date_of_birth'];
                $upmember->id_number          = $request['id_number'];
                $upmember->email              = $request['email'];
                $upmember->password           = $request['password'] ? Hash::make($request['password']) : $upmember->password;
                $upmember->save();
                $successmessage = __("user.infoupdate");
                return $this-> returnData('success',$successmessage);

            } else {
                $errormessage = __("user.usernotexist");
                return $this->returnError('E001', $errormessage);
            }
        }else{
            $errormessage = __("user.usernotexist");
            return $this->returnError('E001', $errormessage);
        }
    }



    /**
     *  updating profile
     */

    public function updateProfile(Request $request)
    {
        $upmember = Member::where('id', $request->member_id)->first();
        if ($upmember) {
            if($upmember->username){
                $validator = Validator::make($request->all(), [
                    'username'            => 'required',
                    'phone'               => 'required|unique:members,phone,' . $upmember->id,
                    'date_of_birth'       => 'required',
                    'nationality'         => 'required',
                    'email'               => 'required|unique:members,email,' . $upmember->id,
                    'password'            => 'required|min:6',
                ],
                [
                    'username.required'            => __("user.username"),
                    'phone.required'               => __("user.phone"),
                    'phone.unique'                 => __("user.unique_phone"),
                    'date_of_birth.required'       => __("user.date_of_birth"),
                    'nationality.required'         => __("user.nationality"),
                    'email.required'               => __("user.email"),
                    'email.unique'                 => __("user.unique_email"),
                    'password.required'            => __("user.password"),
                    'password.min'                 => __("user.max_password"),
                ]);
                if ($validator->fails()) {
                    $code = $this->returnCodeAccordingToInput($validator);
                    return $this->returnValidationError($code, $validator);
                }
                $upmember->username        = $request['username'];
                $upmember->phone           = $request['phone'];
                $upmember->date_of_birth   = $request['date_of_birth'];
                $upmember->nationality     = $request['nationality'];
                $upmember->email           = $request['email'];
                $upmember->password        = $request['password'] ? Hash::make($request['password']) : $upmember->password;
                $upmember->save();
                $successmessage = __("user.infoupdate");
                return $this-> returnData('success',$successmessage);

            } else {
                $errormessage = __("user.usernotexist");
                return $this->returnError('E001', $errormessage);
            }
        }else{
            $errormessage = __("user.usernotexist");
            return $this->returnError('E001', $errormessage);
        }
    }
}