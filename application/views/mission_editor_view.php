<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
		<div id="container">
			<h1 class="main_heading">Mission Editor (LIST)</h1>
			<div class="mission_list">
				<? if(empty($missions)){ ?>
					<h2>Sorry no missions to show!</h2>
				<? }else{ ?>
					<table class="table table-bordered table-condensed">
						<thead>
							<tr>
								<th scope="col"><strong>Mission Number</strong></th>
								<th scope="col">Title</th>
								<th scope="col">Description</th>
								<th scope="col">Parameters</th>
							</tr>
						</thead>
						<tbody>
						<? foreach($missions as $mission){ ?>
							<tr>
								<th scope="row"><?=anchor('mission_editor/update/' . $mission['id'], $mission['mission_number'])?></th>
								<td><?=$mission['title']?></td>
								<td><?=$mission['description']?></td>
								<td><pre><code><?=$mission['parameters']?></code></pre></td>
							</tr>
						<? } ?>
						</tbody>
					</table>
				<? } ?>
			</div>
		</div>