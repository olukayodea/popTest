<?php
    class users extends database {
        /**
         * @param string $email  user's email
         */
        public $email = "";

        /**
         * @param string $password  user's password
         */
        public $password = "";

        /**
         * @param string $firstname  user's password
         */
        public $firstname = "";

        /**
         * @param string $lastname  user's password
         */
        public $lastname = "";

        public function getData() {
            if (isset($_SESSION['userData'])) {
                $data = $_SESSION['userData'];

                return array(
                    "success" => true,
                    "data" => $data
                );
            } else {
                return array(
                    "success" => false
                );
            }
        }

        public function logout() {
            // Clear all session variables
            $_SESSION = array();
            
            // Destroy the session
            session_destroy();

            return array(
                "success" => true,
                "message" => "Logged out user successfully"
            );
        }

        public function login() {
            // check if the email and password is not empty
            if ($this->email != "" && $this->password != "") {
                try {
                    $data = $this->getOne("users", $this->email, "email");
                    if ($data) {
                        if ($data == "-1") {
                            $result = array(
                                "success" => false,
                                "message" => "run the setup script in <a href='setup.php' target='_blank'>Setup</a>"
                            );
                        } else {
                            if (password_verify($this->password, $data['password'])) {
                                unset($data['password']);
                                $this->updateOne(
                                    "users",
                                    "login_time",
                                    date('Y-m-d H:i:s'),
                                    $data['ref']
                                );
                                $data['login_time'] = date('Y-m-d H:i:s');
                                $_SESSION['userData'] = $data;

                                $result = array(
                                    "success" => true,
                                    "data" => $data
                                );
                            } else {
                                $result = array(
                                    "success" => false,
                                    "message" => "Email or Password is not correct"
                                );
                            }
                        }
                    } else {
                        $result = array(
                            "success" => false,
                            "message" => "Email or Password is not correct"
                        );
                    }
                } catch (Exception $e) {
                    $result = array(
                        "success" => false,
                        "message" => $e->getMessage()
                    );
                }

            } else {
                $result = array(
                    "success" => false,
                    "message" => "Email or Password is empty"
                );
            }

            return $result;
        }

        public function signup() {
            // check if the email and password is not empty
            if ($this->email != "" && $this->password != "" && $this->firstname != "" && $this->lastname != "") {
                try {
                    if ($this->checkExixst("users", "email", $this->email) == 0) {
                        $insert = $this->insert(
                            "users",
                            array(
                                "firstname" => $this->firstname,
                                "lastname" => $this->lastname,
                                "email" => $this->email,
                                "password" => password_hash($this->password, PASSWORD_DEFAULT)
                            ),
                            true
                        );
                        if ($insert == "-1") {
                            $result = array(
                                "success" => false,
                                "message" => "run the setup script in <a href='setup.php' target='_blank'>Setup</a>"
                            );
                        } else if ($insert) {
                            $data = $this->getOne("users", $this->email, "email");

                            unset($data['password']);
                            $this->updateOne(
                                "users",
                                "login_time",
                                date('Y-m-d H:i:s'),
                                $data['ref']
                            );
                            $data['login_time'] = date('Y-m-d H:i:s');
                            $_SESSION['userData'] = $data;

                            $result = array(
                                "success" => true,
                                "data" => $data
                            );
                        } else {
                            $result = array(
                                "success" => false,
                                "message" => "Error occured while saving user"
                            );
                        }
                    } else {
                        $result = array(
                            "success" => false,
                            "message" => "this user exisit"
                        );
                    }
                } catch (Exception $e) {
                    $result = array(
                        "success" => false,
                        "message" => $e->getMessage()
                    );
                }

            } else {
                $result = array(
                    "success" => false,
                    "message" => "Email or Password is empty"
                );
            }

            return $result;
        }
    }
?>