<?php

class MY_Controller extends CI_Controller {

    public $admin;

    public function CurrentUrlNew() {
        return base_url(uri_string());
    }

    public function __construct() {
        parent::__construct();
        $this->admin = $this->session->userdata("admin");

        $ca = $this->ControllerDotAction();
        $controller_name = $this->ControllerName();

        $protected_area = array();
        $protected_controllers = array();

        //###########################################
        $protected_controllers[] = "a";
        $protected_controllers[] = "q";
        $protected_controllers[] = "servicecat";



        //###########################################

//        if (!isset($this->admin)) {
//            if (in_array($ca, $protected_area)) {
//                redirect("admin/index");
//            }
//            if (in_array($controller_name, $protected_controllers)) {
//                redirect("admin/index");
//            }
//        }

//        $this->setTitle("No Title , Please use _title variable");
//        $this->setKeywords("SitePoint");
//        $this->setDescription("SitePoint");
//        $this->setPageTitle("No Title");
//        $this->setMenu(array("text" => "Home Page", "url" => site_url("welcome/index")));
//        $this->data['view_name'] = $this->view();
//        $role = $this->session->userdata("role");
//        $role = isset($role) ? $role : "front";
//        $this->set_role($role);
//        $this->data['template'] = "front";
//        
//
//
//        $this->setMessage(null);
//        $this->setUser(null);
        $this->__header = "layout/header";
        $this->__footer = "layout/footer";
        $this->__aheader = "layout/a_header";
        $this->__afooter = "layout/a_footer";
        $this->data['__ca'] = $this->ControllerSlashAction();
        $this->data['CurrentUrlNew']= $this->CurrentUrlNew();
        //  $this->form_validation->set_error_delimiters('<div class="error">', '</div>');
    }

    public function _Json($status) {
        return $this->output
                        ->set_content_type('application/json')
                        ->set_header("Access-Control-Allow-Origin:*")
                        ->set_header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE")
                        ->set_header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With")
                        ->set_output(json_encode($status));
    }

    public function PageCount($count, $PAGE_SIZE = 10) {
        return (int) (($count - 1) / $PAGE_SIZE) + 1;
    }

    public function Pageno() {
        return isset($_GET['pageno']) ? $_GET['pageno'] : 1;
    }

    public function GetParam($param, $default = "") {
        return array_key_exists($param, $_GET) && $this->HasValue($_GET[$param]) ? $_GET[$param] : $default;
    }

    public $__header;
    public $__footer;
    public $__aheader;
    public $__afooter;

    public function aheader() {
        return $this->__aheader;
    }

    public function afooter() {
        return $this->__afooter;
    }

    public function header() {
        return $this->__header;
    }

    public function footer() {
        return $this->__footer;
    }

    public function setTitle($title) {
        $this->data['_title'] = $title;
    }

    public function setUser($user) {
        $this->data['user'] = $user;
    }

    public function setMenu($menu) {
        $this->data['_menu'] = $menu;
    }

    public function setMenuArray($menu_array) {
        $this->data['menu_array'] = $menu_array;
    }

    public function setPageTitle($pagetitle) {
        $this->data['_pagetitle'] = $pagetitle;
    }

    public function setKeywords($keywords) {
        $this->data['_keywords'] = $keywords;
    }

    public function setDescription($description) {
        $this->data['_description'] = $description;
    }

    public function getMessage() {
        return isset($this->data['message']) ? $this->data['message'] : null;
    }

    public function hasMessage() {
        return isset($this->data['message']) ? true : false;
    }

    public function setMessage($message) {
        $this->data['message'] = $message;
    }

    public function role() {
        return $this->data['role'];
    }

    public function set_role($role) {
        $this->data['role'] = $role;
    }

    public function template() {
        return $this->data['template'];
    }

    public function set_template($template) {
        $this->data['template'] = $template;
    }

    public function welcome() {
        return "welcome/header";
    }

    public function map($key, $value) {
        $this->data[$key] = $value;
    }

    public function view() {
        $ci = & get_instance();
        $controller = $ci->router->fetch_class();
        $action = $ci->router->fetch_method();
        return strtolower($controller) . "/" . $action;
    }

    public function script() {
        $ci = & get_instance();
        $controller = $ci->router->fetch_class();
        $action = $ci->router->fetch_method();
        return strtolower($controller) . "/" . $action . "_script";
    }

    public function ActionName() {
        $ci = & get_instance();
        return strtolower($ci->router->fetch_method());
    }

    public function ControllerName() {
        $ci = & get_instance();
        return $ci->router->fetch_class();
    }

    public function ControllerDotAction() {
        $ci = & get_instance();
        return strtolower($ci->router->fetch_class()) . "." . strtolower($ci->router->fetch_method());
    }

    public function ControllerSlashAction() {
        $ci = & get_instance();
        return strtolower($ci->router->fetch_class()) . "/" . strtolower($ci->router->fetch_method());
    }

    public function CA() {
        return $this->ControllerSlashAction();
    }

    public function render($view = null, $data = null) {
        $view = $view == null ? $this->view() : $view;
        $data = $data == null ? $this->data : $data;
        $this->load->view($view, $data);
    }

    public function IsPost() {
        $ci = &get_instance();
        return $ci->input->server("REQUEST_METHOD") == "POST";
    }

    public function IsGet() {
        $ci = &get_instance();
        return $ci->input->server("REQUEST_METHOD") == "GET";
    }

    //Check if input is empty or not
    public function HasValue($input) {
        return $input != null && isset($input) && trim($input) != "";
    }

    public function Populate(&$model) {
        foreach ($_POST as $key => $value) {
            if (property_exists($model, $key)) {
                $model->$key = htmlspecialchars(trim($_POST[$key]), ENT_QUOTES, 'UTF-8');
            }
        }//
    }

    public function PopulateRaw(&$model) {
        $p = (array) json_decode(file_get_contents("php://input"));
        //   print_r($p);
        foreach ($p as $key => $value) {
            if (property_exists($model, $key)) {
                $model->$key = htmlspecialchars(trim($p[$key]), ENT_QUOTES, 'UTF-8');
            }
        }//
    }

    public function PopulateGet(&$model) {
        foreach ($_GET as $key => $value) {
            if (property_exists($model, $key)) {
                $model->$key = htmlspecialchars(trim($_GET[$key]), ENT_QUOTES, 'UTF-8');
            }
        }//
    }

    public function ValueOrDefaultInteger($value, $default = 0) {
        if ($value != null && isset($value) && ValidationRules::validate_integer($value)) {
            return $value;
        }
        return $default;
    }

    public function ValueOrDefault($value, $default) {
        if ($value != null && isset($value) && trim($value) != "") {
            return $value;
        }
        return $default;
    }

    private $pagesize = 10;

    public function getPagesize() {
        return $this->pagesize;
    }

    public function setPagesize($pagesize) {
        $this->pagesize = $pagesize;
    }

    public $data = array();

    public function CurrentUrl() {
        $ci = & get_instance();
        $controller = $ci->router->fetch_class();
        $action = $ci->router->fetch_method();
        return site_url(strtolower($controller) . "/" . $action);
    }

    ###########################################################

    public function _validateMobile($input) {
        return preg_match('/^[6789]{1}[0-9]{9}$/', $input);
    }

    public function InputGet($param, $default = 0) {
        $result = $this->input->get($param);
        if (isset($result) && strlen($result) > 0)
            return $result;
        return $default;
    }

}

?>