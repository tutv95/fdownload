<?php

namespace App\Http\Controllers;

use App\UserX;
use DateTimeZone;
use FriesMail;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Storage;
use stdClass;

class TestController extends Controller {
	public function home() {
		return view( 'welcome' );
	}

	public function reg( Request $request ) {
		$email = $request->input( 'email' );
		$name  = $request->input( 'name' );
		$pass  = $request->input( 'password' );

		$users = UserX::all()->where( 'email', $email );
		if ( $users->count() > 0 ) {
			$response        = new stdClass();
			$response->error = true;
			$response->error_msg
			                 = 'Email này đã được đăng ký';

			return response()->json( $response );
		}

		$user = UserX::create( [
			'email'     => $email,
			'name'      => $name,
			'pass'      => md5( $pass ),
			'unique_id' => uniqid( '', true ),
		] );

		$response           = new stdClass();
		$response->error    = false;
		$response->uid      = $user->getAttribute( 'unique_id' );
		$user_x             = new stdClass();
		$user_x->name       = $user->getAttribute( 'name' );
		$user_x->email      = $user->getAttribute( 'email' );
		$user_x->created_at = $user->getAttribute( 'created_at' )
		                           ->setTimezone( new DateTimeZone( 'Asia/Ho_Chi_Minh' ) )
		                           ->format( 'Y-m-d H:m:i' );
		$user_x->updated_at = $user->getAttribute( 'updated_at' )
		                           ->setTimezone( new DateTimeZone( 'Asia/Ho_Chi_Minh' ) )
		                           ->format( 'Y-m-d H:m:i' );

		$response->user = $user_x;

		return response()->json( $response );
	}

	public function login( Request $request ) {
		$email = $request->input( 'email' );
		$pass  = $request->input( 'password' );

		$users = UserX::all()->where( 'email', $email );

		if ( $users->count() > 0 ) {
			$user = $users->last();

			if ( md5( $pass ) != $user->getAttribute( 'pass' ) ) {
				$response            = new stdClass();
				$response->error     = true;
				$response->error_msg = 'Sai mật cmn khẩu rồi :]]';

				return response()->json( $response );
			}

			$response           = new stdClass();
			$response->error    = false;
			$response->uid      = $user->getAttribute( 'unique_id' );
			$user_x             = new stdClass();
			$user_x->name       = $user->getAttribute( 'name' );
			$user_x->email      = $user->getAttribute( 'email' );
			$user_x->created_at = $user->getAttribute( 'created_at' )
			                           ->setTimezone( new DateTimeZone( 'Asia/Ho_Chi_Minh' ) )
			                           ->format( 'Y-m-d H:m:i' );
			$user_x->updated_at = $user->getAttribute( 'updated_at' )
			                           ->setTimezone( new DateTimeZone( 'Asia/Ho_Chi_Minh' ) )
			                           ->format( 'Y-m-d H:m:i' );

			$response->user = $user_x;

			return response()->json( $response );
		}

		$response        = new stdClass();
		$response->error = true;
		$response->error_msg
		                 = 'Éo có tài khoản này hoặc gõ sai mật cmn khẩu rồi :]]';

		return response()->json( $response );
	}

	public function sendMail() {
		$subject   = 'Đây là email test thử thôi :v';
		$html
		           = '<h1>Đây là tai tồ</h1><p>Còn đây là nội dung nhé</p>. Tiếp theo là <a href="http://google.com.vn">Link</a>.';
		$friesMail = new FriesMail( $subject, $html );
		$friesMail
			->addTo( 'minhquylt95@gmail.com' )
			->setFrom( 'fries.uet@gmail.com' )
			->setFromName( 'Fries Team' );

		sendMail( $friesMail );
	}

	public function storageFile( Request $request ) {
		$file = $request->file( 'txt' );

		$disk = Storage::get( 'de' );
		dd( $disk );

		dd( str_slug( $file->getClientOriginalName() ) );

		Storage::put(
			'ok/de' . $file->getClientOriginalName(),
			file_get_contents( $file->getRealPath() )
		);
	}

	public function getTimetable() {
		$user = '13020499';
		$pass = 'hhw95mrt';

		$url = 'http://dangkyhoc.daotao.vnu.edu.vn/dang-nhap';
		$uni = uni_get( $url );

		$headers           = $uni->headers;
		$set_cookie        = $headers['Set-Cookie'];
		$verificationToken = explode( '__RequestVerificationToken=',
			$set_cookie )[1];
		$verificationToken = explode( ';',
			$verificationToken )[0];


		$body_ = [
			'LoginName'                  => $user,
			'Password'                   => $pass,
			'__RequestVerificationToken' => $verificationToken,
		];

		$uni_2 = uni_post( $url, $headers, $body_ );

		dd( $uni_2 );
	}
}
