<?php

namespace App\Controllers;

class HomeController extends Controller {
    public function index(): void {
        if (is_logged_in()) {
            redirect('/dashboard');
        } else {
            redirect('/login');
        }
    }
}
