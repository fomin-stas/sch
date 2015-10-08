<?
$TEACHER_ACCESS = ($arResult["TEACHER_ACCESS"]=="Y");
?>
<div class="stat">
<?
if(is_array($arResult["PROPERTIES"]["TEACHER"]))
{
	?>
	<div class="tRow">
		<div class="c tl"></div>
		<div class="c tr"></div>
		<div class="c bl"></div>
		<div class="c br"></div>
		<div class="tRowHead"><?=GetMessage("TEACHER")?>:</div>
		<div class="tRowTxt">
			<div class="personId">
				<?
				if(!empty($arResult["PROPERTIES"]["TEACHER"]["PERSONAL_PHOTO"]))
				{
					$renderImage = CFile::ResizeImageGet($arResult["PROPERTIES"]["TEACHER"]["PERSONAL_PHOTO"], Array("width" => 30, "height" => 30),BX_RESIZE_IMAGE_EXACT); 
					?>
					<div class="ava darkBorder">
						<img src="<?=$renderImage["src"]?>" alt="" />
						<div class="c tl"></div>
						<div class="c tr"></div>
						<div class="c bl"></div>
						<div class="c br"></div>
					</div>
					<?
				}
				?>
				<div class="personName">
					<?=$arResult["PROPERTIES"]["TEACHER"]["LAST_NAME"]." ".$arResult["PROPERTIES"]["TEACHER"]["NAME"]." ".$arResult["PROPERTIES"]["TEACHER"]["SECOND_NAME"]?><br/>
					<i><?=$arResult["PROPERTIES"]["TEACHER"]["WORK_POSITION"]?></i>
				</div>
			</div>
		</div>
	</div>
	<?
}
?>
	<div class="tRow">
		<div class="c tl"></div>
		<div class="c tr"></div>
		<div class="c bl"></div>
		<div class="c br"></div>
		<div class="tRowHead"><?=GetMessage("SUBJECT")?>:</div>
		<div class="tRowTxt">
			<?=$arResult["PROPERTIES"]["SUBJECT"]["VALUE"]["NAME"]?>
		</div>
	</div>
	<div class="tRow">
		<div class="c tl"></div>
		<div class="c tr"></div>
		<div class="c bl"></div>
		<div class="c br"></div>
		<div class="tRowHead"><?=GetMessage("AUDIENCE")?>:</div>
		<div class="tRowTxt">
			<?=$arResult["PROPERTIES"]["AUDIENCE"]["NAME"]?> (<?=$arResult["PROPERTIES"]["AUDIENCE"]["STAGE"]["NAME"]?>)
		</div>
	</div>
	<div class="tRow">
		<div class="c tl"></div>
		<div class="c tr"></div>
		<div class="c bl"></div>
		<div class="c br"></div>
		<div class="tRowHead"><?=GetMessage("DATE")?>:</div>
		<div class="tRowTxt"><?=$arResult["DATE_FORMATED"]?></div>
	</div>
	<div class="tRow">
		<div class="c tl"></div>
		<div class="c tr"></div>
		<div class="c bl"></div>
		<div class="c br"></div>
		<div class="tRowHead"><?=GetMessage("LESSON_TIME")?>:</div>
		<div class="tRowTxt"><?=$arResult["ACTIVE_FROM"]?> - <?=$arResult["ACTIVE_TO"]?></div>
	</div>
	<div class="tRowRound <?=$TEACHER_ACCESS?"editable":""?>">
		
		<?
		if($TEACHER_ACCESS)
		{
			?><ul class="editOptions"><li class="edit"><a href="#" onclick="showLessonParamsWindow(<?=$arResult["ID"]?>); return false;" title="<?=GetMessage("LESSON_EDIT")?>"></a></li></ul><?
		}
		?>
		<div class="tRow">
			<div class="c tl"></div>
			<div class="c tr"></div>
			<div class="c bl"></div>
			<div class="c br"></div>
			<div class="tRowHead"><?=GetMessage("LESSON_THEME")?>:</div>
			<div class="tRowTxt">
				<h3 class="alt"><?=(!empty($arResult["PROPERTIES"]["LESSON_THEME"]["VALUE"]))?"&laquo;".$arResult["PROPERTIES"]["LESSON_THEME"]["VALUE"]."&raquo;":"&mdash;"?></h3>
			</div>
		</div>
		<div class="tRow">
			<div class="c tl"></div>
			<div class="c tr"></div>
			<div class="c bl"></div>
			<div class="c br"></div>
			<div class="tRowHead"><?=GetMessage("LESSON_DESCR")?>:</div>
			<div class="tRowTxt"><?=(!empty($arResult["DETAIL_TEXT"]))?$arResult["DETAIL_TEXT"]:"&mdash;"?></div>
		</div>
	</div>
	<div class="tRow tRowRound <?=$TEACHER_ACCESS?"editable":""?>">
		<?
		if($TEACHER_ACCESS)
		{
			?><ul class="editOptions"><li class="edit"><a href="#" onclick="showHomeworkEditWindow(<?=$arResult["ID"]?>); return false;" title="<?=GetMessage("LESSON_EDIT")?>"></a></li></ul><?
		}
		?>
		
		<div class="c tl"></div>
		<div class="c tr"></div>
		<div class="c bl"></div>
		<div class="c br"></div>
		<div class="tRowHead"><?=GetMessage("HOME_WORK")?>:</div>
		<div class="tRowTxt">
			<?
			if(!empty($arResult["PROPERTIES"]["HOME_WORK"]["VALUE"]))
			{
				?><?=$arResult["PROPERTIES"]["HOME_WORK"]["VALUE"]?><?
			}
			else
			{
				?><div style='color:#999; text-align:center;'><?=GetMessage("NO_HOME_WORK")?></div><?
			}
			
			if(count($arResult["PROPERTIES"]["HOME_WORK_FILES"]["VALUE"])>0)
			{
				?><div><h4><?=GetMessage("HW_FILES_LIST")?></h4>
				<ul style='list-style:none; margin:0; padding:0;'>
				<?
				foreach($arResult["PROPERTIES"]["HOME_WORK_FILES"]["VALUE"] as $fid)
				{
					$file = CFile::GetFileArray($fid);
					?><li style='margin:0;'><a href="<?=$file["SRC"]?>"><?=(!empty($file["DESCRIPTION"]))?$file["DESCRIPTION"]:$file["ORIGINAL_NAME"]?></a></li><?
				}
				?>
				</ul>
				</div><?
			}
			
			?>
		</div>
	</div>
</div>



<div class="workTypes">
	<h3 class="alt"><?=GetMessage("CLASS_WORK")?></h3>
	<?
	$countMarkTypes = count($arResult["PROPERTIES"]["MARK_TYPES"]["VALUE"]);
	if(is_array($arResult["PROPERTIES"]["MARK_TYPES"]["VALUE"]) && $countMarkTypes>0)
	{
		?>
		<table cellpadding="0" cellspacing="0" class="logsTable">
		<?
		foreach($arResult["PROPERTIES"]["MARK_TYPES"]["VALUE"] as $i=>$mt)
		{
			?>
			<tr <?=($i%2)?"class='even'":""?>>
				<td class="abbr"><?=$mt["SHORT"]?></td>
				<td class="editable">
					<div class="editableTxt">
						<?
						if($TEACHER_ACCESS)
						{
						?>
						<ul class="editOptions">
							<li class="edit"><a href="#" onclick="editWorkType('<?=$mt["VALUE"]?>',<?=$arResult["ID"]?>); return false;" title="<?=GetMessage("LES_EDIT_WORK")?>"></a></li>
							<li class="delete"><a href="#" onclick="deleteWorkType('<?=$mt["VALUE"]?>','<?=GetMessage("LESS_WORK_DELETE_QUESTION")?>'); return false;" title="<?=GetMessage("LES_DELETE_WORK")?>"></a></li>
						</ul>
						<?
						}
						?>
						<?=$mt["FULL"]?>
					</div>
				</td>
			</tr>
			<?
		}
		?>
		</table>
		<?
	}
	else
	{
		?><div style='color:#999; text-align:center;'><?=GetMessage("NO_WORK_TYPES")?></div><?
	}
	
	if($TEACHER_ACCESS)
	{
		?><div class="addType"><a href="#" onclick="addWorkType(<?=$arResult["ID"]?>); return false;"><?=GetMessage("LES_ADD_WORK")?></a></div><?
	}
	?>
</div>


<?
if(is_array($arResult["JOURNAL"]) && count($arResult["JOURNAL"])>0)
{
?>

<div class="logs">
	<h3 class="alt"><?=GetMessage("JOURNAL")?></h3>
	<div class="logsContainer">
		<table cellpadding="0" cellspacing="0" class="logsTable">
			<col class="first" />
			<col />
			<?if($countMarkTypes>0){?><colgroup span="<?=$countMarkTypes?>" /><?}?>
			<colgroup span="1" />
			<thead>
				<tr>
					<td><?=GetMessage("LES_N")?></td>
					<td><?=GetMessage("STUDENTS")?></td>
					<?
					foreach($arResult["PROPERTIES"]["MARK_TYPES"]["VALUE"] as $mt)
					{
						?><td><?=$mt["SHORT"]?></td><?
					}
					?>
					<td><?=GetMessage("LESSON_COMMENT")?></td>
				</tr>
			</thead>
			<tbody>
				<?
				$marks = CSchool::GetMarks();
				foreach($arResult["JOURNAL"] as $i=>$s)
				{
				?>
				<tr <?=($i%2)?"class='even'":""?>>
					<td><?=($i+1)?></td>
					<td class="names"><?=$s["LAST_NAME"]." ".$s["NAME"]." ".$s["SECOND_NAME"]?></td>
					<?
					foreach($s["MARKS"] as $k => $m)
					{
					?>
					<td class="editableCell" 
						title="<?=$arResult["MARK_TYPES"][$k]["FULL"]?>" <?=(empty($m["COLOR"]))?"":"style='background-color:".$m["COLOR"]."'"?> 
						id="mark_<?=$s["ID"]?>_<?=$k?>_parent" 
						<?if($TEACHER_ACCESS){?>onclick="showMarkSelect(<?=$s["ID"]?>,'<?=$k?>');  return false;"<?}?>
						>
						<?
						if($TEACHER_ACCESS)
						{
						?>
						<div style='display:none' id="mark_<?=$s["ID"]?>_<?=$k?>"><?=$m["VALUE"] ?></div>
						<select name='mark[<?=$s["ID"]?>][<?=$k?>]' id='mark[<?=$s["ID"]?>][<?=$k?>]' onblur="showMarkDiv(<?=$s["ID"]?>,'<?=$k?>')" onchange="setMark(<?=$s["ID"]?>,'<?=$k?>')">
							<option value=''></option>
							<?
							foreach($marks as $k=>$v)
							{
								?><option value="<?=$v?>" <?=($m["VALUE"]==$v)?"selected":""?>><?=$v?></option><?
							}
							?>
						</select>
						<?
						}
						else
						{
							?><?=$m["VALUE"]?><?
						}
						?>
						
					</td>
					<?
					}
					?>
					<td class="comment <?=$TEACHER_ACCESS?"editable":""?>">
						<div class="commentTxt editableTxt">
							<?
							if($TEACHER_ACCESS)
							{
								if(empty($s["STUDENT_WORK_COMMENT"]))
								{
									?><ul class="editOptions"><li class="add"><a title="<?=GetMessage("LES_ADD_S_COMMENT")?>" href="#" onclick="addCommentStudent(<?=$s["ID"]?>,<?=$arResult["ID"]?>); return false;"></a></li></ul><?
								}
								else
								{
									?><ul class="editOptions"><li class="edit"><a title="<?=GetMessage("LES_EDIT_S_COMMENT")?>" href="#" onclick="addCommentStudent(<?=$s["ID"]?>,<?=$arResult["ID"]?>); return false;"></a></li></ul><?
								}
							}
							?>
							<?=$s["STUDENT_WORK_COMMENT"]?>
						</div>
					</td>
				</tr>
				<?
				}
				?>
				
				
			</tbody>
		</table>
	</div>
</div>
<?
}
?>