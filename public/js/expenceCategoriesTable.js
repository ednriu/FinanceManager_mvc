		const $tableID = $('#table');
		const $BTN = $('#przycisk');
		const $EXPORT = $('#export');

		const $newCategory = `
		<tr>
					<td class="pt-3-half" contenteditable="true">Nowa Kategoria</td>
					<td class="pt-3-half">
					  <span class="table-up"><a href="#!" class="indigo-text"><i class="fas fa-long-arrow-alt-up"
							aria-hidden="true"></i></a></span>
					  <span class="table-down"><a href="#!" class="indigo-text"><i class="fas fa-long-arrow-alt-down"
							aria-hidden="true"></i></a></span>
					</td>
					<td>
					  <span class="table-remove"><button type="button"
						  class="btn btn-danger btn-rounded btn-sm my-0">Usuń</button></span>
					</td>
		</tr>`;
		

		//poniższe wartości ustawiane są jako "." ponieważ jeśli używkonik nie kliknął nic, to program nie ma do czego porównywać kategori
		var $activeCellContent =".";
		var $activeCategoryContent=".";
		
		var newCategory = $tableID.find('tr:last td:first').text();
		var activeCellContent;		


		//-----------------------------------------
		//dodawanie nowej kategori jeśli nie istnieje
		 $('.table-add').on('click', 'i', () => {
		   const $clone = $tableID.find('tbody tr').last().clone(true);
		   newCategory = $tableID.find('tr:last td:first').text();

		   
		   if ((newCategory !== 'Nowa Kategoria')) {
			 $('#table table tbody').append($newCategory);
			 newCategory = $tableID.find('tr:last td:first').text();			 
			 //przewijanie do nowo dodanej kategorii
			 var elem = $('#table');
			 var maxScrollTop = elem[0].scrollHeight - elem.outerHeight();
			 $('#table').scrollTop(maxScrollTop);
			 //dodanie komunikatu
			 $feedback = "Proszę nadać nazwę nowej kategorii."
			 $("#feedback").show().text($feedback).addClass('alert-success').removeClass('alert-danger'); 
			 //Ponieważ nazwa kategorii musi być zmieniona, skrypt ukrywa przyciski w linijce nowej kategorii oraz przycisk dodania kolejnej
			 $('.table-add').find('a').hide();
			 $tableID.find('tr:last .table-up').hide();
			 $tableID.find('tr:last .table-down').hide();
		   }
		 });

		//----------------------------------------
		//Zapisanie danych do zmiennych z klikniętej komórki
		$tableID.on('click','tbody tr td',function() {
			$activeCellContent = $(this).html();
			$activeCellCollumnNumber = $(this).index();
			$activeCellRowNumber = parseInt($(this).closest("tr").index()+1);
			if ($activeCellCollumnNumber==1) {
					$activeCategoryContent = $(this).parents('tr').find('td:first').html();
					$activeMaxLimit = $(this).parents('tr').find('td:nth-child(2)').html();
				};
		});
		
		//------------------------------------------
		//Zmiana Danych	w dowolnej komórce	
		$tableID.on('focusout','tbody tr td',function() {
			if ($activeCellContent!=$(this).html()){

				//jeżeli strzałka w górę jest ukryta znaczy, że to jest zmiana nowej kategorii
				if ($(this).parents('tr').find('td:nth-child(3) .table-up').is(":hidden")) 
				{
				
					//zmiana dotyczy zmiany kategorii. Musi być ona różna od "Nowa Kategoria"
					if ($activeCellCollumnNumber==0) 
					{					
						$newCategoryName = $(this).html();
						$maxLimit = ($(this).parents('tr').find('td:nth-child(2)').html());
						
						$.ajax({
							type: 'POST',
							url: '/Settings/addCategory',
							data: {
								categoryName: $newCategoryName,
								categoryType: 'expences',
								max: $maxLimit
								},
							success: function(res, msg) {
							  res = $.parseJSON(res);
							  if (res.isCategoryDoubled) {
								$tableID.find('tr:last td:first').text("Nowa Kategoria");
								$("#feedback").show().text(res.message).addClass('alert-danger').removeClass('alert-success').delay(3000).queue(function(n) {$(this).hide(); n();});
							  }
							  else
							  {
								$("#feedback").show().text(res.message).addClass('alert-success').removeClass('alert-danger').delay(3000).queue(function(n) {$(this).hide(); n();});
								$('.table-add').find('a').show();
								$tableID.find('tr:last .table-up').show();
								$tableID.find('tr:last .table-down').show();
							  };
							},
							error : function() {
								console.log("Wystąpił błąd z połączeniem");
							}
						});						
					};								
				};
				
				
				//jeżeli strzałka w górę jest widoczna oznacza to zmianę kategori różnej od "Nowa Kategoria"
				if ($(this).parents('tr').find('td:nth-child(3) .table-up').is(":visible")) 
				{
					//zmiana w kolumnie katerogii
						if ($activeCellCollumnNumber==0) {
							$maxLimit = ($(this).parents('tr').find('td:nth-child(2)').html());	
							$newCategoryName = ($(this).parents('tr').find('td:nth-child(1)').html());
							$oldCategoryName = $activeCellContent;
						};
						
					//zmiana w kolumnie limitów	
						if ($activeCellCollumnNumber==1) {
							$maxLimit = ($(this).parents('tr').find('td:nth-child(2)').html());
							$newCategoryName = $activeCategoryContent;
							$oldCategoryName = $activeCategoryContent;												
						};
					
					//Wywołanie polecenia AJAX dla obydwu zmian
							$.ajax({
								type: 'POST',
								url: '/Settings/updateCategory',
								data: {
									categoryType: 'expences',
									newCategoryName: $newCategoryName,
									oldCategoryName: $oldCategoryName,
									max: $maxLimit
									},
								success: function(res, msg) {
								  res = $.parseJSON(res);
								  if (res.isCategoryDoubled) {
									$tableID.find('tr:nth-child('+$activeCellRowNumber+') td:nth-child(1)').text($activeCellContent);
									$("#feedback").show().text(res.message).addClass('alert-danger').removeClass('alert-success').delay(3000).queue(function(n) {$(this).hide(); n();});;
								  }
								  else
								  {
									$("#feedback").show().text(res.message).addClass('alert-success').removeClass('alert-danger').delay(3000).queue(function(n) {$(this).hide(); n();});;
								  };
								},
								error : function() {
									console.log("Wystąpił błąd z połączeniem");
								}
							});					
				};				
			}
		});

		//-------------------------------------------
		//usuwanie Kategorii
		 $tableID.on('click', '.table-remove', function () {
			$category = $(this).parents('tr').find('td:first-child').html();
			$(this).parents('tr').detach();
			
				 $.ajax({
					type: 'POST',
					url: '/Settings/removeIncomeCategory',
					data: {category: $category},
					success: function(res, msg) {
						res = $.parseJSON(res);
						if (res.successfullyRemoved)
						{
							$("#feedback").show().text(res.message).addClass('alert-success').removeClass('alert-danger').delay(3000).queue(function(n) {$(this).hide(); n();});
						}
						else
						{
							$("#feedback").show().text(res.message).addClass('alert-success').removeClass('alert-danger').delay(3000).queue(function(n) {$(this).hide(); n();});;
						};
						
						
					}
				  });
			});
		
		 
		//--------------------------------------------
		//przesuwanie kategori do góry
		 $tableID.on('click', '.table-up', function () {
		   const $row = $(this).parents('tr');
		   $bottomCategory=$(this).parents('tr').find('td:first').html();
		   $topCategory=$(this).parents('tr').prev('tr').find('td:first').html();
		   if ($row.index() === 0) {
			 return;
		   }
		   $.ajax({
					type: 'POST',
					url: '/Settings/replaceIncomeCategoriesIds',
					data: {
									firstCategoryName: $topCategory,
									secondCategoryName: $bottomCategory
									},
					success: function(res,msg) {

					},
					error : function (jqXHR, exception) {
							var msg = '';
							if (jqXHR.status === 0) {
								msg = 'Not connect.\n Verify Network.';
							} else if (jqXHR.status == 404) {
								msg = 'Requested page not found. [404]';
							} else if (jqXHR.status == 500) {
								msg = 'Internal Server Error [500].';
							} else if (exception === 'parsererror') {
								msg = 'Requested JSON parse failed.';
							} else if (exception === 'timeout') {
								msg = 'Time out error.';
							} else if (exception === 'abort') {
								msg = 'Ajax request aborted.';
							} else {
								msg = 'Uncaught Error.\n' + jqXHR.responseText;
							}
							alert(msg);
						},
				  });		   
		   $row.prev().before($row.get(0));
		 });
		 
		//---------------------------------------------
		//przesuwanie kategorii w dół
		 $tableID.on('click', '.table-down', function () {
			$topCategory=$(this).parents('tr').find('td:first').html();
			$bottomCategory=$(this).parents('tr').next('tr').find('td:first').html();
			 $.ajax({
					type: 'POST',
					url: '/Settings/replaceIncomeCategoriesIds',
					data: {
									firstCategoryName: $topCategory,
									secondCategoryName: $bottomCategory
									},
					success: function(res,msg) {

					},
					error : function (jqXHR, exception) {
							var msg = '';
							if (jqXHR.status === 0) {
								msg = 'Not connect.\n Verify Network.';
							} else if (jqXHR.status == 404) {
								msg = 'Requested page not found. [404]';
							} else if (jqXHR.status == 500) {
								msg = 'Internal Server Error [500].';
							} else if (exception === 'parsererror') {
								msg = 'Requested JSON parse failed.';
							} else if (exception === 'timeout') {
								msg = 'Time out error.';
							} else if (exception === 'abort') {
								msg = 'Ajax request aborted.';
							} else {
								msg = 'Uncaught Error.\n' + jqXHR.responseText;
							}
							alert(msg);
						},
				  });	
		   const $row = $(this).parents('tr');
		   $row.next().after($row.get(0));		   
		 });
