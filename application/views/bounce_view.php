<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
		<div class="container">
			<h1 class="main_heading">PHP Bounce Mission List</h1>
			<div class="main_sections">
				<div class="bounce_list">
					<? if(!empty($missions)){ ?>
						<? foreach($missions as $mission){ ?>
							<div class="specific_mission"><?=anchor('bounce/mission/' . $mission['id'], $mission['title'])?></div>
						<? } ?>
					<? }else{ ?>
						<h2>No missions yet!</h2>
					<? } ?>
				</div>
			</div>
		</div>