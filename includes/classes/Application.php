<?php

class Application extends Battleships{
	
	protected $env;
	protected $game_end = false;
	
	public function __construct($width, $height, $ships){
		parent::__construct($width, $height, $ships);
		
		if(php_sapi_name() == 'cli'){
			$this->env = 'cli';
		}else{
			$this->env = 'web';
		}
	}
	
	/*
		Run application
	*/
	public function run(){
		$this->load_game();
		$this->display_game();
	}
	
	/*
		Load game state
	*/
	private function load_game(){
		if($this->env == 'cli'){
			$this->make_grid();
			$this->add_ships();
		}else{
			session_set_cookie_params(3600);
			session_start();
			if(isset($_SESSION['gamestate'])){
				$data = unserialize($_SESSION['gamestate']);
				if($data['grid']){
					$this->set_grid($data['grid']);
				}
				if($data['shoots']){
					$this->set_shoots($data['shoots']);
				}
			}else{
				$this->make_grid();
				$this->add_ships();
				$this->save_game();
			}
		}
	}
	
	/*
		Save game state
	*/
	private function save_game(){
		$data = array(
			'grid' => $this->get_grid(),
			'shoots' => $this->get_shoots(),
		);
		$_SESSION['gamestate'] = serialize($data);
	}
	
	/*
		Reset game
	*/
	
	private function reset_game(){
		if($this->env == 'cli'){
			$this->make_grid();
			$this->add_ships();
		} else{
			unset($_SESSION['gamestate']);
		}
		
	}
	
	/*
		Show the game
	*/
	protected function display_game(){
		if($this->env == 'cli'){
			$this->gisplay_cli();
		}else{
			$this->gisplay_web();
		}
	}
	
	/*
		Show CLI mode
	*/
	private function gisplay_cli(){
		
		$continue = true;
		$show_ships = false;
		do{
			system('clear');
			$grid = $this->display_grid($show_ships);
			$show_ships = false;
			print $grid;
			if($shoot){
				print $shoot."\n\n";
				$shoot = false;
			}
			print "Help func: show, reset, exit\n";
			print "Enter coordinates (row, col), e.g. A5: ";
			$handle = fopen ("php://stdin","r");
			$coords = trim(fgets($handle));
			
			switch(trim($coords)){
				case 'quit':
				case 'exit':
					$continue = false;
				break;
				case 'new':
				case 'reset':
					$this->reset_game();
				break;
				case 'show':
					$show_ships = true;
				break;
				default:
					$shoot = $this->shoot($coords);
				break;
			}
			if($this->game_end){
				$continue = false;
			}
		}while($continue);
		
		system('clear');
		print $this->display_grid($show_ships);
		print $shoot."\n\n";
	}
	
	/*
		Show Apache mode
	*/
	private function gisplay_web(){
		$coords = isset($_POST['coords'])?trim($_POST['coords']):false;
		$show_ships = $shoot = false;
		if($coords){
			switch($coords){
				case 'show':
					$show_ships = true;
				break;
				case 'new':
				case 'reset':
					$this->reset_game();
				break;
				default:
					$shoot = $this->shoot($coords);
					$this->save_game();
				break;
			}
		}
		$show_ships = $coords && $coords == 'show'?true:false;
		
		include(INC_PATH.'/views/display.php');
		
		if($this->game_end){
			$this->reset_game();
		}
	}
}