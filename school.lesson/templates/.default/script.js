function hide(object)
{
	object.style.display = "none";
}

function show(object)
{
	object.style.display = "";
}



function showLessonParamsWindow(ELEMENT_ID) 
{ 
   (new BX.CDialog({
		content_url: '/bitrix/components/school/school.lesson/w_lesson_params.php?ELEMENT_ID='+ELEMENT_ID+'&url='+document.location,
		width: 500,
		height: 250
   })).Show(); 
   return false;
}

function showHomeworkEditWindow(ELEMENT_ID) 
{ 
   (new BX.CDialog({
		content_url: '/bitrix/components/school/school.lesson/w_homework_edit.php?ELEMENT_ID='+ELEMENT_ID,
		width: 700,
		height: 500
   })).Show(); 
   return false;
} 

function addWorkType(ELEMENT_ID) 
{ 
   (new BX.CDialog({
		content_url: '/bitrix/components/school/school.lesson/w_add_work_type.php?ELEMENT_ID='+ELEMENT_ID,
		width: 500,
		height: 80
   })).Show(); 
   return false;
} 

function deleteWorkType(workType,deleteQuestion)
{
	if(confirm(deleteQuestion))
	{
		function ShowResult(data)
		{
			location.reload();
			return false;
		}
		var TID = jsAjaxUtil.PostData(document.location, {'mode':'ajax','action':'deleteWorkType','workType':workType}, ShowResult);
	}
	return false;
}

function showMarkDiv(user_id, mark_type)
{
	var oMark = document.getElementById("mark["+user_id+"]["+mark_type+"]");
	var oMarkDiv = document.getElementById("mark_"+user_id+"_"+mark_type);
//	hide(oMark);
//	show(oMarkDiv);
	return false;
}

function setMark(user_id, mark_type)
{
	var oMark = document.getElementById("mark["+user_id+"]["+mark_type+"]");
	var oMarkDiv = document.getElementById("mark_"+user_id+"_"+mark_type);
	var oMarkDivParent = document.getElementById("mark_"+user_id+"_"+mark_type+"_parent");
	oMarkDiv.innerHTML = oMark.value;
	
	function ShowResult(data)
	{
		//location.reload();
		oMarkDivParent.style.backgroundColor = data;
//		hide(oMark);
//		show(oMarkDiv);
		return false;
	}
	var TID = jsAjaxUtil.PostData(document.location, {'mode':'ajax','action':'setMark','mark_type':mark_type,'user_id':user_id, 'mark':oMark.value}, ShowResult);
	return false;
}

function showMarkSelect(user_id, mark_type)
{
	var oMark = document.getElementById("mark["+user_id+"]["+mark_type+"]");
	var oMarkDiv = document.getElementById("mark_"+user_id+"_"+mark_type);
//	show(oMark);
	oMark.focus();
//	hide(oMarkDiv);
	return false;
}

function addCommentStudent(user_id,lesson_id)
{
	(new BX.CDialog({
		content_url: '/bitrix/components/school/school.lesson/w_comment_student.php?USER_ID='+user_id+'&ELEMENT_ID='+lesson_id,
		width: 400,
		height: 150
   })).Show();
   return false;
}

function editWorkType(workType,lesson_id)
{
	(new BX.CDialog({
		content_url: '/bitrix/components/school/school.lesson/w_edit_work_type.php?mark_type='+workType+'&ELEMENT_ID='+lesson_id,
		width: 450,
		height: 80
   })).Show();
   return false;
}