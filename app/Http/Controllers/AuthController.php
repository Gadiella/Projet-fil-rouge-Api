<?php


namespace App\Http\Controllers;
use App\Mail\PasswordResetOtpMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\AccountDeletedMail;
use App\Mail\AccountUpdatedMail;
use Illuminate\Support\Facades\Cache;

use App\Mail\OtpCodeMail;
use App\Mail\PasswordResetSuccessMail;
use App\Mail\RegistrationSuccessMail;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\ModelNotFoundException;



class AuthController extends Controller


{
    public function destroy($id)
{
    try {
        // Récupérer l'utilisateur par son ID
        $user = User::findOrFail($id);

        // Envoyer l'email avant de supprimer l'utilisateur
        Mail::to($user->email)->send(new AccountDeletedMail($user->username));

        // Supprimer l'utilisateur
        $user->delete();

        // Retourner un message de succès
        return response()->json(['message' => 'Utilisateur supprimé avec succès et email envoyé'], 200);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        // Gérer le cas où l'utilisateur n'est pas trouvé
        return response()->json(['error' => 'Utilisateur non trouvé avec l\'ID : ' . $id], 404);

    } catch (\Exception $e) {
        // Journaliser l'erreur et retourner une réponse
        Log::error('Erreur lors de la suppression de l\'utilisateur : ' . $e->getMessage());
        return response()->json(['error' => 'Échec de la suppression de l\'utilisateur : ' . $e->getMessage()], 500);
    }
}

    public function update(Request $request, $id)
    {
        try {
            // Validation des données
            $validatedData = $request->validate([
                'email' => 'required|email|unique:users,email,' . $id,
                'username' => 'required|string|max:255|unique:users,username,' . $id,
                'password' => 'nullable|string|min:6',
            ]);
    
            // Log de l'ID utilisateur
            Log::info("ID utilisateur à mettre à jour : " . $id);
    
            // Récupération de l'utilisateur
            $user = User::findOrFail($id);
    
            // Log de l'utilisateur trouvé
            Log::info("Utilisateur trouvé : " . $user->username);
    
            // Mise à jour des informations de l'utilisateur
            $user->email = $validatedData['email'];
            $user->username = $validatedData['username'];
    
            if (!empty($validatedData['password'])) {
                $user->password = Hash::make($validatedData['password']);
                $newPassword = $validatedData['password'];
            } else {
                $newPassword = null;
            }
    
            // Enregistrement des modifications
            $user->save();
    
            // Envoi de l'email
            Mail::to($user->email)->send(new AccountUpdatedMail($user->username, $newPassword));
    
            // Log pour confirmer la réussite
            Log::info("Compte utilisateur mis à jour : " . $user->username);
    
            return response()->json(['message' => 'Compte utilisateur mis à jour et email envoyé'], 200);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->validator->errors()], 422);
        } catch (ModelNotFoundException $e) {
            Log::error('Utilisateur non trouvé avec l\'ID : ' . $id);
            return response()->json(['error' => 'Utilisateur non trouvé avec l\'ID : ' . $id], 404);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de l\'utilisateur : ' . $e->getMessage());
            return response()->json(['error' => 'Échec de la mise à jour de l\'utilisateur : ' . $e->getMessage()], 500);
         }
 
     }
    

     public function getUsersExcludingAdmin(Request $request)
     {
         try {
             // Récupérer tous les utilisateurs sauf ceux avec le rôle 'admin' ou dont le rôle est NULL
             $users = User::select('id', 'username', 'password', 'email')
                 ->where(function($query) {
                     $query->where('role', '!=', 'admin')
                           ->orWhereNull('role'); // Vérifie si le rôle est NULL
                 })
                 ->get()
                 ->makeVisible('password'); // Rendre le mot de passe visible temporairement
     
             return response()->json($users, 200);
         } catch (\Exception $e) {
             return response()->json(['error' => 'Erreur lors de la récupération des utilisateurs : ' . $e->getMessage()], 500);
         }
     }
     
    
    
 public function store(Request $request)
{
    try {
        // Validation des données
        $validatedData = $request->validate([
            'email' => 'required|email|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        // Création de l'utilisateur sans hachage du mot de passe
        $user = User::create([
            'email' => $validatedData['email'],
            'username' => $validatedData['username'],
            'password' => $validatedData['password'], // En clair (non recommandé)
        ]);

        // Envoi de l'email avec le surnom et mot de passe en clair
        Mail::to($user->email)->send(new OtpCodeMail($user->username, $validatedData['password']));

        return response()->json(['message' => 'Utilisateur créé et email envoyé'], 201);
    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation errors: ' . json_encode($e->errors()));
        return response()->json(['error' => $e->validator->errors()], 422);
    } catch (\Exception $e) {
        Log::error('Erreur lors de la création de l\'utilisateur : ' . $e->getMessage());
        return response()->json(['error' => 'Échec de la création de l\'utilisateur : ' . $e->getMessage()], 500);
    }
}

    
    public function register(Request $request) 
    {
        // Validation des données sans le champ password_confirmation
        try {
            $validatedData = $request->validate([
                'name' => 'nullable|string|max:255',
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:6',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error:', ['errors' => $e->errors()]);
            return response()->json(['error' => 'Validation error.', 'messages' => $e->errors()], 422);
        }
    
        try {
            // Log des données reçues pour l'inspection
            Log::info('Données reçues pour l\'inscription : ', $validatedData);
    
            // Création de l'utilisateur
            $user = User::create([
                'name' => $validatedData['name'],
                'username' => $validatedData['username'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
            ]);
    
            // Log de l'utilisateur créé
            Log::info('Utilisateur créé avec succès : ', ['user' => $user]);
    
            // Envoi de l'email de confirmation
            Mail::to($user->email)->send(new RegistrationSuccessMail());
    
            return response()->json(['message' => 'Inscription réussie, un email de confirmation a été envoyé.'], 201);
        } catch (\Exception $e) {
            // Logging the error
            Log::error('Erreur lors de la création de l\'utilisateur :', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Une erreur est survenue lors de l\'inscription.'], 500);
        }
    }
    public function login(Request $request)
    {
        // Valider les données de la requête
        $credentials = $request->validate([
            'username' => 'required_without:email|string',  // Requis si l'email est absent
            'email' => 'required_without:username|string|email',  // Requis si le nom d'utilisateur est absent
            'password' => 'required|string',
        ]);
    
        // Rechercher l'utilisateur par email ou nom d'utilisateur
        $user = User::where('email', $request->email)
                    ->orWhere('username', $request->username)
                    ->first();
    
        // Vérifier si l'utilisateur existe et que le mot de passe correspond
        if (!$user || $user->password !== $request->password) {
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }
    
        // Si l'utilisateur existe et que le mot de passe est correct
        $token = $user->createToken('authToken')->plainTextToken;
    
        // Retourner l'utilisateur et le token dans la réponse
        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 200);
    }
    
    
    public function changePassword(Request $request)
    {
        try {
            // Valider les entrées (email et mot de passe)
            $validatedData = $request->validate([
                'email' => 'required|email',  // Validation de l'email
                'password' => 'required|min:8', // Validation du mot de passe
            ]);
    
            // Trouver l'utilisateur par email
            $user = User::where('email', $validatedData['email'])->first();
    
            // Si l'utilisateur n'existe pas
            if (!$user) {
                return response()->json(['message' => 'Utilisateur non trouvé'], 404);
            }
    
            // Mettre à jour le mot de passe de l'utilisateur sans hashage
            $user->password = $validatedData['password']; // Stocker le mot de passe en clair
            $user->save();
    
            // Retourner un message de succès
            return response()->json([
                'message' => 'Mot de passe changé avec succès. Vous pouvez maintenant vous connecter.',
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    

    public function sendOtpCode(Request $request) 
    {
        try {
            // Valider l'email de l'utilisateur
            $request->validate([
                'email' => 'required|email',
            ]);
    
            // Trouver l'utilisateur par email
            $user = User::where('email', $request->email)->first();
    
            // Si l'utilisateur n'existe pas, retourner une erreur
            if (!$user) {
                return response()->json(['message' => 'Utilisateur non trouvé'], 404);
            }
    
            // Générer un OTP aléatoire à 6 chiffres
            $otpCode = rand(100000, 999999);
    
            // Définir la durée de validité de l'OTP (ex. 15 minutes)
            $otpExpiresAt = now()->addMinutes(15);
    
            // Mettre à jour l'utilisateur avec l'OTP et la date d'expiration
            $user->otp_code = $otpCode;
            $user->otp_expires_at = $otpExpiresAt;
            $user->save();
    
            // Envoyer l'OTP par email
            Mail::to($user->email)->send(new PasswordResetOtpMail($user->username, $otpCode));
    
            // Retourner un message de succès
            return response()->json(['message' => 'OTP envoyé à votre adresse email'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    

    public function verifyOtpCode(Request $request)
    {
        try {
            // Valider les entrées (email et otp_code)
            $validatedData = $request->validate([
                'email' => 'required|email',
                'otp_code' => 'required|numeric',
            ]);
    
            // Trouver l'utilisateur par email
            $user = User::where('email', $validatedData['email'])->first();
    
            // Si l'utilisateur n'existe pas
            if (!$user) {
                Log::info('Utilisateur non trouvé : ' . $validatedData['email']);
                return response()->json(['message' => 'Utilisateur non trouvé'], 404);
            }
    
            // Logs pour débogage
            Log::info('Utilisateur trouvé : ' . $user->email);
            Log::info('OTP de l\'utilisateur : ' . $user->otp_code);
            Log::info('Expiration de l\'OTP : ' . $user->otp_expires_at);
    
            // Vérifier si l'OTP est expiré
            $otpExpiresAt = new Carbon($user->otp_expires_at);
            if (Carbon::now()->greaterThan($otpExpiresAt)) {
                Log::info('OTP expiré pour ' . $user->email);
                return response()->json(['message' => 'OTP expiré'], 400);
            }
    
            // Vérifier la correspondance de l'OTP
            if ($user->otp_code !== $validatedData['otp_code']) {
                Log::info('OTP invalide pour ' . $user->email);
                return response()->json(['message' => 'OTP invalide'], 400);
            }
    
            // OTP validé avec succès
            Log::info('OTP vérifié avec succès pour ' . $user->email);
            return response()->json([
                'message' => 'OTP vérifié avec succès. Vous pouvez maintenant réinitialiser votre mot de passe',
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
}
