<?php

class chart extends msDB {
	function __construct($connection) {
		$this->messsage = "initialize class";
		if ($connection ==true) {
			$radiochecked = $this->connect();
		}
	}
	function __destruct() {
		unset($radiochecked);
	}

        function getChart($REQUEST){
            $grid = new grid(true);
            $grid->setTable("grafik");
            $grid->setGroupBy("tahun");

            $grid->addField(Array(
                            "field"=>"tahun",
                            "name"=>"tahun"
            ));
            $grid->addField(Array(
                            "field"=>"SUM(page_view)",
                            "name"=>"page_view"
            ));
            $grid->addField(Array(
                            "field"=>"SUM(page_visit)",
                            "name"=>"page_visit"
            ));
            return $grid->doRead($REQUEST); 
        }
}
?>
