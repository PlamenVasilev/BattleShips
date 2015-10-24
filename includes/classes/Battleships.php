<?php

class Battleships{
	private $grid = array();
	private $shoots = array();
	
	private $width;
	private $height;
	private $ships;
	private $letters;
	
	public function __construct($width, $height, $ships){
		$this->width = $width;
		$this->height = $height;
		$this->ships = $ships;
		$this->letters = range("A","Z");
	}
	
	/*
		Fill new empty grid
	*/
	protected function make_grid(){
		for($x=1; $x<=$this->width; $x++){
			for($y=0; $y<$this->height; $y++){
				$this->grid[$x][$this->letters[$y]] = false;
				$this->shoots[$x][$this->letters[$y]] = false;
			}
		}
	}
	
	/*
		Fill empty grid with ships
	*/
	protected function add_ships(){
		foreach($this->ships as $s_num=>$size){
			$retrys = 0;
			do{
				$retrys++;
				$retry = false;
				$dir = mt_rand(1,4);
				$add = array();
				switch($dir){
					case 1:
						// right
						$x = mt_rand(1, $this->width-$size);
						$y = mt_rand(0, $this->height-1);
						
						for($x_coord = $x; $x_coord<$x+$size; $x_coord++){
							if($this->grid[$x_coord][$this->letters[$y]]){
								$retry = true;
								break 2;
							}else{
								$add[$x_coord][$this->letters[$y]] = $s_num+1;
							}
						}
					break;
					case 2:
						// down
						$x = mt_rand(1, $this->width);
						$y = mt_rand(0, $this->height-$size-1);
						
						for($y_coord = $y; $y_coord<$y+$size; $y_coord++){
							if($this->grid[$x][$this->letters[$y_coord]]){
								$retry = true;
								break 2;
							}else{
								$add[$x][$this->letters[$y_coord]] = $s_num+1;
							}
						}
					break;
					case 3:
						// left
						$x = mt_rand(1+$size, $this->width);
						$y = mt_rand(0, $this->height-1);
						
						for($x_coord = $x; $x_coord>$x-$size; $x_coord--){
							if($this->grid[$x_coord][$this->letters[$y]]){
								$retry = true;
								break 2;
							}else{
								$add[$x_coord][$this->letters[$y]] = $s_num+1;
							}
						}
					break;
					case 4:
						// up
						$x = mt_rand(1, $this->width);
						$y = mt_rand(0+$size, $this->height-1);
						
						for($y_coord = $y; $y_coord>$y-$size; $y_coord--){
							if($this->grid[$x][$this->letters[$y_coord]]){
								$retry = true;
								break 2;
							}else{
								$add[$x][$this->letters[$y_coord]] = $s_num+1;
							}
						}
					break;
				}
				
				if(!$retry){
					foreach($add as $x=>$x_vals){
						foreach($x_vals as $y=>$val){
							$this->grid[$x][$y] = $val;
						}
					}
				}
				if($retrys>100){
					die("Cannot generate ships grid\n");
				}
			}while($retry);
		}
	}
	
	/*
		Displays the grid with or without ships
	*/
	protected function display_grid($show_ships = false){
		$width = 3;
		$grid =  str_pad(' ', $width);
		foreach($this->grid as $x=>$y_vals){
			$grid .= str_pad($x, $width);
		}
		$grid .= "\n";
		
		foreach(array_keys($this->grid[1]) as $y){
			$grid .= str_pad($y, $width);
			
			foreach(array_keys($this->grid) as $x){
				if($show_ships){
					$grid .= str_pad($this->grid[$x][$y]?$this->grid[$x][$y]:' ', $width);
				}else{
					$grid .= str_pad($this->shoots[$x][$y]?$this->shoots[$x][$y]:'.', $width);
				}
			}
			$grid .= "\n";
		}
		$grid .= "\n";
		
		return $grid;
	}
	
	/*
		Shoot on coords on the grid
	*/
	protected function shoot($coords){
		if(preg_match('/(\w)(\d+)/', strtoupper($coords), $match)){
			$x = $match[2];
			$y = $match[1];
			$ynum = ord($y)-65;
			
			if( $x<=0 || $x>$this->width || $ynum<0 || $ynum>=$this->height ){
				return "ERROR: wrong coordinates";
			}else{
				
				if($this->grid[$x][$y]){
					$ship = $this->grid[$x][$y];
					$this->grid[$x][$y] = false;
					$ship_sunk = true;
					$cnt_ship_points = 0;
					foreach($this->grid as $x_vals){
						foreach($x_vals as $x_val){
							if($x_val == $ship){
								$ship_sunk = false;
							}
							if($x_val){
								$cnt_ship_points++;
							}
						}
					}
					$this->shoots[$x][$y] = 'X';
					if(!$cnt_ship_points){
						$this->game_end = true;
						return "Well done! You completed the game in ".$this->get_shoots_cnt()." shots!";
					}else if($ship_sunk){
						return "Sunk!";
					}else{
						return "HIT : yeyyy!";
					}
				}else if($this->shoots[$x][$y]){
					return "Already shoot there!";
				} else {
					$this->shoots[$x][$y] = '-';
					return "MISS!";
				}
			}
		}else{
			return "ERROR: wrong coordinates";
		}
	}
	
	/*
		Return the number of shoots
	*/
	protected function get_shoots_cnt(){
		$cnt = 0;
		foreach($this->shoots as $x=>$x_val){
			foreach($x_val as $y){
				if($y){
					$cnt++;
				}
			}
		}
		return $cnt;
	}
	
	/*
		Get current grid
	*/
	protected function get_grid(){
		return $this->grid;
	}
	
	/*
		Set current grid
	*/
	protected function set_grid($grid){
		$this->grid = $grid;
	}
	
	/*
		Get current shoots
	*/
	protected function get_shoots(){
		return $this->shoots;
	}
	
	/*
		Set current shoots
	*/
	protected function set_shoots($shoots){
		$this->shoots = $shoots;
	}
}