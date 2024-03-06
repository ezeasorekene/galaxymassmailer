<?php

namespace ezeasorekene\App\Models;

use Josantonius\Session\Facades\Session as Session;

class AuthenticateModel extends ApplicantModel
{
    protected int $applicantNo;

    protected string $token;

    protected string $expires_on;

    public function isLoggedIn()
    {
        if (Session::has('applicantNo')) {
            $this->applicantNo = Session::get('applicantNo');
            $applicant = ApplicantModel::find($this->applicantNo);

            if (!$applicant) {
                Session::remove('applicantNo');
                return false;
            }

            return true;
        }

        $this->applicantNo = 0;
        return false;
    }

    public function getApplicantNo()
    {
        return $this->applicantNo;
    }

    public function logout()
    {

        $this->applicantNo = 0;
        if (Session::has('applicantNo') || Session::has('email')) {
            Session::remove('applicantNo');
            Session::remove('email');
            return true;
        }

        return false;
    }


}