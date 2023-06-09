<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Register extends BaseController
{
    public function index()
    {
        return view('register');
    }

    public function sendmail($to, $subject, $message)
    {
        $to                 = $to;
        $subject            = $subject;
        $message            = $message;

        $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host       = 'smtp.googlemail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'gawljr@gmail.com'; // replace with your email address
            $mail->Password   = 'nxfwtumrknlngkig'; // replace with your email password
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 465;

            $mail->setFrom('gawljr@gmail.com', 'Niagahoster Tutorial'); // replace with your email address and name
            $mail->addAddress($to);
            $mail->addReplyTo('gawljr@gmail.com', 'Niagahoster Tutorial'); // replace with your email address and name

            // Email Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;

            $mail->send();

            // Success message
            session()->setFlashdata('success', 'Congratulations, email has been sent successfully!');
            return redirect()->to('/login');
            echo "ema";
        } catch (Exception $e) {
            // Error message
            session()->setFlashdata('error', 'Failed to send email. Error: ' . $mail->ErrorInfo);
            return redirect()->to('/login');
        }
    }

    public function process()
    {
        helper(['form', 'url']);

        $rules = [
            'name' => 'required',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'profile_picture' => 'uploaded[profile_picture]|max_size[profile_picture,1024]|ext_in[profile_picture,jpg,png,jpeg]'
        ];

        if ($this->validate($rules)) {
            $userModel = new UserModel();

            $profilePicture = $this->request->getFile('profile_picture');
            $newProfilePictureName = $profilePicture->getRandomName();

            $data = [
                'name' => $this->request->getVar('name'),
                'email' => $this->request->getVar('email'),
                'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
                'profile_picture' => $newProfilePictureName,
                'verification_token' => bin2hex(random_bytes(16))
            ];
            $userModel->save($data);

            $profilePicture->move('uploads', $newProfilePictureName);

            $verificationLink = base_url("register/verifyEmail/{$data['verification_token']}");

            $to                 = $this->request->getVar('email');
            $subject            = 'Verifikasi Akun';
            $message            = "klik link ini gais {$verificationLink}";

            $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->isSMTP();
            $mail->Host       = 'smtp.googlemail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'gawljr@gmail.com'; // replace with your email address
            $mail->Password   = 'nxfwtumrknlngkig'; // replace with your email password
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 465;

            $mail->setFrom('gawljr@gmail.com', 'Niagahoster Tutorial'); // replace with your email address and name
            $mail->addAddress($to);
            $mail->addReplyTo('gawljr@gmail.com', 'Niagahoster Tutorial'); // replace with your email address and name

            // Email Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $message;

            $mail->send();

            // Success message
            session()->setFlashdata('success', 'Congratulations, email has been sent successfully!');
            return redirect()->to('/login');
            echo "ema";
        } catch (Exception $e) {
            // Error message
            session()->setFlashdata('error', 'Failed to send email. Error: ' . $mail->ErrorInfo);
            return redirect()->to('/login');
        }

            // Send email verification here
            

            // $email = \Config\Services::email();
            // $email->setTo($data['email']);
            // $email->setSubject('Email Verification');
            // $email->setMessage("Click the following link to verify your email: $verificationLink");
            // $email->send();

            return redirect()->to('login')->with('success', 'Registration successful. Please check your email for verification.');
        } else {
            return view('register', ['validation' => $this->validator]);
        }
    }

    public function verifyEmail($token)
    {
        $userModel = new UserModel();
        $user = $userModel->where('verification_token', $token)->first();
        var_dump($token); die;

        if ($user) {
            // Verify user's email
            $userModel->update($user['id'], ['is_verified' => 1, 'verification_token' => null]);

            return redirect()->to('login')->with('success', 'Email verification successful. You can now log in.');
        } else {
            return redirect()->to('login')->with('error', 'Invalid verification token.');
        }
    }

    
}
