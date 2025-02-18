<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\support\Facades\Session;

class AppController extends Controller {
    public function index() {
        $user = auth()->user();

        if ( Auth::user()->password_changed_at === null ) {
            return redirect( route( 'change-password' ) );
        } else {
            return view( 'dashboard' );
        }
    }

    public function updatePassword( Request $request ) {
        $request->validate( [
            'new-password' => 'required|min:8|confirmed',
        ] );
        $user = User::find( Auth::id() );

        if ( !$user ) {
            return redirect()->route( 'login' )->with( 'error_message', 'Utilisateur non authentifié' );
        }

        $user->password = Hash::make( $request->input( 'new-password' ) );
        $user->password_changed_at = now();
        // Mettez à jour la date du changement de mot de passe
        $user->save();

        return redirect( route( 'dashboard' ) )->with( 'success_message', 'Votre mot de passe a été mis à jour.' );
    }

    public function showChangePasswordForm() {
        return view( 'enseignant.changer_password' );
        // Remplacez par le nom de votre vue
    }
}
