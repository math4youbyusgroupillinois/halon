<?php

class SetupController extends \BaseController {

	// Todo: BeforeFilter to Check Authorized 401 to Angular

	public function __construct()
	{
		$this->beforeFilter('serviceCSRF');

	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

	public function generate_passwords()
	{
		$admin_pass = Input::get('admin_password');
		$printer_pass = Input::get('printer_password');

		$users = User::where('password', '=', '');

		if ($users->count() > 0)
		{
			$admin_user = User::where('role', '=', 'admin')->first();
			$printer_user = User::where('role', '=', 'printer')->first();

			// set and return both vals to server
			$admin_user->password = Hash::make($admin_pass);
			$admin_user->save();

			$printer_user->password = Hash::make($printer_pass);
			$printer_user->save();

			return Response::json(array('adminRolePass' => $admin_pass, 'printerRolePass' => $printer_pass), 200);
		}
		else {
			return Response::json(array('error_msg' => 'Sorry, Passwords are already Set'), 401);
		}

	}


}