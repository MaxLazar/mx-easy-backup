<div class="mor">

<?php if($message) : ?>
<div class="mor alert notice">
<p><?php print($message); ?></p>
</div>
<?php endif; ?>

				<?=form_open($_form_base."&method=backup_files", array('name'=>'task_list', 'id'=>'task_list'), '')?>
				<?php
		
					$this->table->set_template($cp_pad_table_template);
					$this->table->set_heading(
												array('data' => form_checkbox('select_all', 'true', FALSE, 'class="toggle_all"'), 'width' => '4%'),
												lang('task_name'),
												array('data' => lang('date'), 'width' => '120px'),												
												lang('file_name'),
												lang('size'),
												lang('time'),
												lang('where'),
												array('data' => lang('type'), 'width' => '70px'),
												array('data' => lang('restore'), 'width' => '70px'),
												array('data' => lang('download'), 'width' => '70px')
											);
											
					if($backup_list->num_rows())
					{
						foreach ($backup_list->result() as $row)
						{
							

							$this->table->add_row(
													'<input class="toggle" type="checkbox" name="toggle[]" value="'.$row->backup_id.'"  />',
													$tasks_list[$row->task_id],
													'<strong>'.$this->localize->format_date($datestr, $row->date, TRUE).'</strong>',
												
													$row->backup_name,
													$this->mx_common->format_size($row->size),
													$row->time.lang('second'),
													lang($row->method),
													$row->type,
													(($row->method == 'email_backup' ) ? '' :  '<div id="umenu"><span class="button"><a title="'.lang('restore').'" class="submit" href="'.$_base.'&method='.(($row->type != 'db') ?  'restore_files' : 'restore_db').'&backup_id='.$row->backup_id.'">'.lang('restore').'</a></span></div>'),
													(($row->method != "email_backup") ? '<div id="umenu"><span class="button"><a title="'.lang('download').'"  href="'.$_base.'&method=download&backup_id='.$row->backup_id.'" class="submit">'.lang('download').'</a></span></div>' : '')
												);

						}
					}else{
										$this->table->add_row(
											array('data' => lang('backup_list_empty'), 'colspan' => 10)
										);
					}
					//$method != 'system' || 

				?>
				<div class="cupRunnethOver shun"><?=$this->table->generate()?></div>
				<?=form_submit(array('name' => 'submit', 'value' => lang('delete'), 'class' => 'submit'))?>

				<?=form_close()?>

</div>

<style>
	#umenu span a:hover {
	    color: #fff;
	    font-weight: bold;
	    background: #27343C;
	    text-shadow: 0 -1px 0 rgba(0,0,0,0.2);
	    box-shadow: rgba(0,0,0,0.2) 0 1px 0 0 inset;
	}

	#umenu span a {
	    background: #ABB7C3;
	    color: #fff;
	    font-size: 11px;
	    font-weight: bold;
	    margin: 3px 7px 0 0;
	    padding: 5px 10px 5px 10px;
	    display: block;
	    text-decoration: none;
	    text-transform: uppercase;
	    border-radius: 5px;
	}
</style>