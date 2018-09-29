
<?php
	$a_types = array(
		array(
			'name' => 'לורם־איפסומר',
			'slug' => 'loremipsum'
		),
		array(
			'name' => 'המחולליישן',
			'slug' => 'tion'
		),
		array(
			'name' => 'רצפי אותיות',
			'slug' => 'letters'
		),
		array(
			'name' => 'שילוב תו',
			'slug' => 'char'
		),
		array(
			'name' => 'ניקוד',
			'slug' => 'nikud'
		)
	);

	$a = (isset($_GET['a'])) ? htmlspecialchars($_GET['a']) : 'loremipsum';
	//get_query_var('media_cat')
?>



<section id="hameholel" class="archive-wrap archive-wrap-hameholel">

	<nav class="generators">
		<?php foreach ($a_types as $key => $type): ?>
			<?php $current = ($a==$type['slug'])? ' class="current"' : ''; ?>

			<a<?=$current?> href="<?=add_query_arg(array('a'=>$type['slug']), get_permalink())?>"><?=($a==$type['slug'])? '<h1>' : ''?><?=$type['name']?><?=($a==$type['slug'])? '</h1>' : ''?></a>
		<?php endforeach; ?>
	</nav>

	<div class="cont-wrap cont-wrap-full">
		<section class="content" id="content" itemprop="articleBody">

			<?php switch ($a) {


	/////////////////////////////////////////////////////////////////////////////////////////////////////////
				case 'loremipsum': ?>

					<div class="info">
						<p>חוללו לכם טקסט חסר משמעות בעברית המכיל מילים לפי נושא הפרויקט עליו אתם עובדים. &#39;לורם איפסום&#39; הוא מלל שמשמש כממלא מקום ומיועד לבדיקת הפונט והלייאאוט שלכם והתאמתם למוצר&nbsp;הסופי.</p>
						<p>איך מחולל הלורם־איפסומר עובד: רובוט של אאא מושך טקסט מויקיפדיה לפי הנושא שבחרתם, הטקסט עובר טרנספורמציה בעזרת טכנולוגיית חלל עתידנית, ואז מוצג כאן כטקסט־דמה לשימושכם היצירתי. נסו&nbsp;ותהנו.</p>
					</div>

	<?php
					$the_text = isset($_POST["the_text"]) ? mysql_real_escape_string($_POST['the_text']) : '';
					$topic = isset($_POST["topic"]) ? stripslashes(stripslashes($_POST['topic'])) : '';
					$topic = ('תל אביב'==$topic)? 'תל אביב-יפו' : $topic;
					$amount = isset($_POST["amount"]) ? stripslashes(stripslashes($_POST['amount'])) : '3';
					$htmlize = (isset($_POST["htmlize"])) ? $_POST['htmlize'] : FALSE;
					?>
					<form method="post" action="/resources/hameholel?a=<?=$a?>">
						<input type="hidden" value="loremipsum" name="a">
						<?php
						if (isset($_POST['submit'])) : //sent
							$topic_clean = str_replace(" ", '_', $topic);
							//$_content = file_get_contents("https://he.wikipedia.org/w/api.php?format=xml&action=query&prop=revisions&rvprop=content&titles=".$topic_clean);
							$_content = file_get_contents("http://he.wikipedia.org/w/api.php?action=query&prop=extracts&explaintext=&exsectionformat=plain&format=xml&exchars=".($amount*750)."&titles=".$topic_clean);
							$content = str_replace (array("\r\n", "\n", "\r","-"), ' ', $_content);
							$content = preg_replace("/[^אבגדהוזחטיכךלמםנןסעפףצץקרשת ]/u", '', $content);
							$words = explode(' ', strip_tags(stripslashes(html_entity_decode($content))));
							shuffle($words);
						endif;
						?>
						<div class="settings settings-cols">
							<div class="options">
								<div>
									<label for="topic">בחרו נושא: </label>
									<input type="text" name="topic" id="topic" value="<?=$topic?>" placeholder="למשל: מוזיקה, עיצוב, ירושלים...">
								</div>
								<div>
									<label for="amount">כמות פסקאות: </label>
									<input type="number" name="amount" id="amount" value="<?=$amount?>">
								</div>
								<div>
									<input type="checkbox" name="htmlize" id="htmlize" value="htmlize"<?=($htmlize==TRUE)?' checked':''?>>
									<label for="htmlize">להוסיף תגיות HTML</label>
								</div>
							</div>
							<input type="submit" class="middle" value="<?=(!isset($_POST['submit']))?'חולל!':'חולל בשנית!'?>" name="submit">
						</div>
						<div class="full-box small-text">
							<textarea name="output" class="result"><?php
							$is_refer_page = mb_substr($content, 0, 50); //בדיקה האם זה עמוד פירושים
							if (strpos($is_refer_page,'האם התכוונתם') !== false) {
							    echo 'אחלה נושא (אם אתם שואלים אותנו), אבל ויקיפדיה טוענת שהוא לא מספיק ממוקד.
נסו ללחוץ על הלינק למטה שיוביל אתכם לעמוד פירושים, ואז חזרו לכאן ומקדו את נושא שלכם.';
							} elseif($words && count($words)>30){
								$word_count=1; $sentence_count=1; $paragraph_count=1;
								if($htmlize==TRUE) echo '<p>';
								foreach ($words as $word) {
									if($word!='' && strlen($word)>2){
										echo $word;
										if($word != end($words)){
											if($word_count%20==0) {
												echo '.';
												$sentence_count++;
											} elseif($word_count%10==0) {
												echo ',';
											}
										} else {
											echo '.';
										}
										echo ' ';
										$word_count++;
									}
									if ($sentence_count%7==0){
										if($htmlize==TRUE && $amount>$paragraph_count) echo '</p>';
										echo '

';
										if($htmlize==TRUE && $amount>$paragraph_count) echo '<p>';
										$sentence_count = 1;
										$paragraph_count++;
										if($paragraph_count>$amount) break;
									}
								}
								if($htmlize==TRUE) echo '</p>';
							} elseif (isset($_POST['submit'])) {
								if(count($words)>4) echo 'מממ... לא מצאנו טקסט מהביטוי "'.$topic.'". נסו לחפש נושא אחר. (למשל: חתונה, תל אביב, אלברט איינשטיין...)';
								else echo 'יש להזין נושא ';

							} ?></textarea>
						</div>

					</form>
					<?php if($topic): ?>
						<small>המילים נטענות אקראית מתוך ויקיפדיה מהערך '<a href="http://he.wikipedia.org/wiki/<?=$topic_clean?>"><?=$topic?></a>'.</small>
					<?php endif; ?>

					<?php break;

/////////////////////////////////////////////////////////////////////////////////////////////////////////

				case 'tion': ?>
					<div class="info">
						<p>כתבו משפטים ובחרו את הסיומת שאתם אוהבים. יאלה בלגאן! הצעות לשיפור יתקבלו בברכה.</p>
					</div>

					<?php
					$the_text = isset($_POST["the_text"]) ? stripslashes(stripslashes($_POST['the_text'])) : '';
					$extention = isset($_POST["extention"]) ? stripslashes(stripslashes($_POST['extention'])) : '';
					switch ($extention) {
						case 'oosh':  $extention_name = 'וּש';       break;
						case 'ism':   $extention_name = 'יִזְם';      break;
						case 'ation': $extention_name = 'יֵישֶן';     break;
						case 'chuk':  $extention_name = 'צ&#39;וּק'; break;
						case 'leh':   $extention_name = 'לֶ&#39;ה';  break;
						case 'keh':   $extention_name = 'קֶה';       break;
						case 'chkeh': $extention_name = 'צְ&#39;קֶה'; break;
						case 'chik':  $extention_name = 'צִ&#39;יק'; break;
						case 'nik':   $extention_name = 'נִיק';      break;
						case 'ist':   $extention_name = 'יִסְט';      break;
						case 'istan': $extention_name = 'יִסְטָן';     break;
						case 'iada':  $extention_name = 'יאַדָּה';     break;
						case 'ool':  $extention_name = 'וּל';     break;
						case 'azz':  $extention_name = 'אָז&#39;'; break;
						case 'os':  $extention_name = 'וס'; break;  
						default:      $extention_name = '';         break;
					}
					?>
					<form method="post" action="/resources/hameholel?a=<?=$a?>">
						<input type="hidden" value="tion" name="a">
						<div class="settings">
							<div>
								<label for="extention">סיומת: </label>
								<select name="extention" id="extention">
									<?php $options = array(
										array( 'name'=>'וּש',       'slug'=>'oosh'),
										array( 'name'=>'יִזְם',      'slug'=>'ism'),
										array( 'name'=>'יֵישֶן',     'slug'=>'ation'),
										array( 'name'=>'צ&#39;וּק', 'slug'=>'chuk'),
										array( 'name'=>'לֶ&#39;ה',  'slug'=>'leh'),
										array( 'name'=>'קֶה',       'slug'=>'keh'),
										array( 'name'=>'צְ&#39;קֶה', 'slug'=>'chkeh'),
										array( 'name'=>'צִ&#39;יק', 'slug'=>'chik'),
										array( 'name'=>'נִיק',      'slug'=>'nik'),
										array( 'name'=>'יִסְט',      'slug'=>'ist'),
										array( 'name'=>'יִסְטָן',     'slug'=>'istan'),
										array( 'name'=>'וּל',     'slug'=>'ool'),//ולוגיה יה
										array( 'name'=>'יאַדָּה',     'slug'=>'iada'), //ולוגיה יה
										array( 'name'=>'אָז&#39;',     'slug'=>'azz'),//
										array( 'name'=>'וֹס',     'slug'=>'os')//
									);
									foreach ($options as $key => $option):
										$selected = ($extention==$option[slug]) ? ' selected="selected"':''; ?>
										<option value="<?=$option[slug]?>"<?=$selected?>><?=$option[name]?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
						<div class="rignt-box">
							<p>טקסט מקורי: </p>
							<textarea name="the_text" class="right"><?=($the_text) ? str_replace("rn", "", stripslashes($the_text)) : 'היי, קוראים לי דניאל. צילמתי את הבר מצווה שלי והעליתי לאינסטגרם. כן אני מגניב!'?></textarea>:
						</div>
						<div class="middle-box">
							<input type="submit" class="middle" value="<?=(!isset($_POST['submit']))?'תרגם!':'תרגם בשנית!'?>" name="submit">
						</div>

						<?php
						if (isset($_POST['submit'])) : //sent
							$the_text = htmlspecialchars(stripslashes(strip_tags($the_text))); //cleat it up
							$the_text = str_replace("rn", "", $the_text);
							$words = explode(' ', $the_text); //break into words
							$new_words = array();
							foreach ($words as $key => $word) :
								$word = str_replace(array("'", "\"", "&quot;"), '', $word);
								$last_letter = substr($word, -1); // get last letter
								$_end_marks = '?.,:!-"';
								$end_marks = str_split($_end_marks); //make array
								if(in_array($last_letter, $end_marks)){ //$last_letter
									$word = $last_letter.substr($word, 0, -1); //put mark at begining
								}

								//words to exclude
								$words_to_exclude = array('לא','אין','יש','על','לו','בו','על','בלי','אינו','כל','הן','צריכה','של','-','לנו','כי','אם','כך','ככ','הוא','היא','זה','אי','הם','בא','הלך','בה','לה','מה','רק','לי','את','אחרי','די','אבל','לאחר','או','אז','בין','עד','מי','הן','כן','','','ככה','אני','גם','תל','עם','—','–', 'בר','נתן');

								$excluded_word='';
								foreach ($words_to_exclude as $word_to_exclude) {
									if($word_to_exclude != ''){
										$thiliot = array('כ','ש','ה','ו','כש','וכש','מ','ומ','וה','וש','ב','ד','ל','ול','וב','וה','וכ','');
										foreach ($thiliot as $thilit) {
											if($word==($thilit.$word_to_exclude)){
												$excluded_word = $word;
												break;
											}
										}
									}
								}
								if(preg_match('/[0-9]/', $word)){ //numbers
									$excluded_word = $word;
								}

								if(preg_match('/[A-Za-z]/', $word)){ //english
									$new_words[] = $word.$extention;
								} elseif($excluded_word==''){

									//word replace
									if('אינסטגרם'==$word) $word='אינסט'; //replace hole words
									if('האינסטגרם'==$word) $word='האינסט'; //replace hole words
									if('לאינסטגרם'==$word) $word='לאינסט'; //replace hole words
									if('באינסטגרם'==$word) $word='באינסט'; //replace hole words
									if('פייסבוק'==$word) $word='פייס'; //replace hole words
									if('בפייסבוק'==$word) $word='בפייס'; //replace hole words
									if('לפייסבוק'==$word) $word='לפייס'; //replace hole words
									if('הפייסבוק'==$word) $word='הפייס'; //replace hole words
									if('היי'==$word) $word='הי'; //replace hole words
									if('ביי'==$word) $word='בי'; //replace hole words

									// replace final letters
									$search  = array('ף', 'ך', 'ן', 'ם', 'ץ');
									$replace = array('פ', 'כ', 'נ', 'מ', 'צ');
									$word = str_replace($search, $replace, $word);

									//remove last char if same as first char of extention and letter 'he'
									$last_letter = mb_substr($word, -1, 1, 'utf-8'); // get last letter again
									$first_letter_of_ext = mb_substr($extention_name, 0, 1, 'utf-8'); // get last letter again
									if ($last_letter==$first_letter_of_ext || $last_letter=='ה'){
										$word = mb_substr($word, 0, -1, 'utf-8');
									}

									// add word to array
									$new_words[] = $word.$extention_name;
								} else { //word that is excluded
									$new_words[] = $word;
								}
							endforeach;

							$final_words = array();

							foreach ($new_words as $key => $new_word) :
								$first_letter = substr($new_word, 0, 1); // again get last letter
								if(in_array($first_letter, $end_marks)){ //$last_letter
									$final_words[] = substr($new_word, 1).$first_letter; //put mark at begining
								} else {
									$final_words[] = $new_word;
								}
							endforeach;
						endif;

						?>
						<div class="left-box">
							<p>תוצאה: </p>
							<textarea name="output" class="result"><?php
							if($final_words){
								foreach ($final_words as $value) {
									echo $value.' ';
								}
							} ?></textarea>
						</div>

					</form>
					<?php break;

	/////////////////////////////////////////////////////////////////////////////////////////////////////////
				case 'letters': ?>
					<div class="info">
						<p>מעצבים פונט? חוללו לכם רצפים של אותיות לבדיקת הריווח בין האותיות.</p>
					</div>
					<?php
					$set1 = (isset($_POST["set1"])) ? $_POST['set1'] : '';
					$set2 = (isset($_POST["set2"])) ? $_POST['set2'] : '';
					?>
					<form method="post" action="/resources/hameholel?a=<?=$a?>">
						<input type="hidden" value="letters" name="a">
						<div class="settings settings-cols">
							<?php $options = array(
								array( 'name'=>'א-ת',     'slug'=>'hebrew'),
								array( 'name'=>'0-9',     'slug'=>'numbers'),
								array( 'name'=>'A-Z',     'slug'=>'english_caps'),
								array( 'name'=>'a-z',     'slug'=>'english_small'),
								array( 'name'=>'@#₪%',    'slug'=>'special_chars'),
								array( 'name'=>'.-־', 	  'slug'=>'top_mid_bot'),
								array( 'name'=>'Åßæÿ',    'slug'=>'latin'),
							); ?>
							<div class="options">
								<div>
									<label for="set1">סט תוים ראשון: </label>
									<select name="set1" id="set1">
										<?php foreach ($options as $key => $option):
											$selected = ($set1==$option[slug]) ? ' selected="selected"':''; ?>
											<option value="<?=$option[slug]?>"<?=$selected?>><?=$option[name]?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div>
									<label for="set2">סט תוים שני: </label>
									<select name="set2" id="set2">
										<?php foreach ($options as $key => $option):
											$selected = ($set2==$option[slug]) ? ' selected="selected"':''; ?>
											<option value="<?=$option[slug]?>"<?=$selected?>><?=$option[name]?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
							<input type="submit" class="middle" value="<?=(!isset($_POST['submit']))?'חולל!':'חולל בשנית!'?>" name="submit">
						</div>

						<?php //sets
						switch ($set1) {
							case 'hebrew':
								$current_set_1 = "אבגדהוזחטיכךלמםנןסעפףצץקרשת";
								break;
							case 'numbers':
								$current_set_1 = "0123456789";
								break;
							case 'english_caps':
								$current_set_1 = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
								break;
							case 'english_small':
								$current_set_1 = "abcdefghijklmnopqrstuvwxyz";
								break;
							case 'latin':
								$current_set_1 = "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõö÷øùúûüýþÿ";
								break;
							case 'special_chars':
								$current_set_1 = "°־*״“”׳‘’^−~«»‹›-–—·•=<>−+×¤←→.,_„‚\!?#:;{}[]()₪$€%|&©@";
								break;
							case 'top_mid_bot':
								$current_set_1 = "°־*״−·.,_";
								break;
						}
						switch ($set2) {
							case 'hebrew':
								$current_set_2 = "אבגדהוזחטיכךלמםנןסעפףצץקרשתא";
								break;
							case 'numbers':
								$current_set_2 = "01234567890";
								break;
							case 'english_caps':
								$current_set_2 = "ABCDEFGHIJKLMNOPQRSTUVWXYZA";
								break;
							case 'english_small':
								$current_set_2 = "abcdefghijklmnopqrstuvwxyza";
								break;
							case 'latin':
								$current_set_2 = "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõö÷øùúûüýþÿ";
								break;
							case 'special_chars':
								$current_set_2 = "°־*״“”׳‘’^−~«»‹›-–—·•=<>+×¤←→.,_„‚\!?#:;{}[]()₪$€%|&©@";
								break;
							case 'top_mid_bot':
								$current_set_1 = "°־*״−·.,_";
								break;
						}
						function sets_generator($chars1,$chars2){
							$letters1 = preg_split('//u', $chars1, -1, PREG_SPLIT_NO_EMPTY);
							$letters2 = preg_split('//u', $chars2, -1, PREG_SPLIT_NO_EMPTY);
							foreach ($letters1 as $key => $letter_1) {
								foreach ($letters2 as $key => $letter_2) {
									echo $letter_1.$letter_2;
								}
							}
						}
						?>
						<div class="full-box big-text">
							<textarea name="output" class="result"><?php sets_generator($current_set_1,$current_set_2) ?></textarea>
						</div>

					</form>
					<small></small>

					<?php break;

	/////////////////////////////////////////////////////////////////////////////////////////////////////////
				case 'char': ?>
					<div class="info">
						<p>מעצבים פונט? שלבו תו בתוך רצף תווים לבדיקת צמדי־אותיות.</p>
					</div>
					<?php
					$set1 = (isset($_POST["set1"])) ? $_POST['set1'] : '';
					$set2 = (isset($_POST["set2"])) ? $_POST['set2'] : '';
					?>
					<form method="post" action="/resources/hameholel?a=<?=$a?>">
						<input type="hidden" value="letters" name="a">
						<div class="settings settings-cols">
							<div class="options">
								<div>
									<label for="set1">תו: </label>
									<input type="text" name="set1" id="set1" placeholder="לדוגמא: א" value="<?=$set1?>" />
								</div>
								<div>
									<label for="set2">רצף תווים: </label>
									<textarea name="set2" id="set2" placeholder="לדוגמא: אבגדהוזח..."><?=$set2?></textarea>
								</div>
							</div>
							<input type="submit" class="middle" value="<?=(!isset($_POST['submit']))?'חולל!':'חולל בשנית!'?>" name="submit">
						</div>

						<?php //sets
						function sets_generator($set1,$set2){
							$char = $set1;
							$letters = preg_split('//u', $set2, -1, PREG_SPLIT_NO_EMPTY);
							echo $char;
							foreach ($letters as $key => $letter) {
								echo $letter.$char;
							}
						}
						?>
						<div class="full-box big-text">
							<textarea name="output" class="result"><?php sets_generator($set1,$set2) ?></textarea>
						</div>

					</form>
					<small></small>

					<?php break;

	/////////////////////////////////////////////////////////////////////////////////////////////////////////
				case 'nikud': ?>
					<div class="info">
						<p>מעצבים פונט? השתמשו במחולל הזה לבדיקת הניקוד של האותיות באותיות הנפוצות.</p>
					</div>

					<?php
					$spaces = (isset($_POST["spaces"])) ? $_POST['spaces'] : FALSE;
					$dagesh = (isset($_POST["dagesh"])) ? $_POST['dagesh'] : FALSE;
					$breakline = (isset($_POST["breakline"])) ? $_POST['breakline'] : TRUE;
					?>
					<form method="post" action="/resources/hameholel?a=<?=$a?>">
						<input type="hidden" value="nikud" name="a">
						<div class="settings settings-cols">
							<div class="options">
								<div>
									<input type="checkbox" name="spaces" id="spaces" value="spaces"<?=($spaces==TRUE)?' checked':''?>>
									<label for="spaces">רווחים בין אותיות</label>
								</div>
								<div>
									<input type="checkbox" name="dagesh" id="dagesh" value="dagesh"<?=($dagesh==TRUE)?' checked':''?>>
									<label for="dagesh">לא לחזור על דגשים</label>
								</div>
								<div>
									<input type="checkbox" name="breakline" id="breakline" value="breakline"<?=($breakline==TRUE)?' checked':''?>>
									<label for="breakline">שורה חדשה לכל אות</label>
								</div>
							</div>
							<input type="submit" class="middle" value="<?=(!isset($_GET['submit']))?'חולל!':'חולל בשנית!'?>" name="submit">
						</div>

						<?php
						function unicoder_array( $from='', $to='',$include = array(), $exclude = array(), $order='numeric' ){
							$content='';
							$unicode_list = array();
							if(!empty($include)){ //include chars
								$unicode_list = $include;
							}
							if($from!='' && $to!=''){
								for ($i=hexdec($from); $i <= hexdec($to); $i++) {  //put range into array
									$unicode_list[]= dechex($i);
								}
							}
							if(!empty($exclude)){ //exclude chars
								$_unicode_list = array_diff($unicode_list, $exclude); // delete from array
								$unicode_list = $_unicode_list;
							}
							if($order=='numeric')
								asort($unicode_list); //sort array numericly (hex)

							return $unicode_list;
						}

						function unicoder_output($unicode=array(), $simple=TRUE, $tag='', $class=''){
							if ($simple==FALSE){
								$content .= '<div style="width:60px;height:60px; float:right; text-align:center;">
									<span style="font-size:22px; display:block;">';
							}
							$content .= '&#x'.$unicode.';';
							if ($simple==FALSE){
								$content .= '</span>';
								$content .= '<em style="font-size:11px;">'.$unicode.'</em>
									</div>';
							}
							return $content;
						}

						function nikud_unicoder($spaces, $dagesh, $breakline){
							$spaces = ($spaces=='spaces') ? ' ' : '';
							$breakline_break = ($breakline=='breakline') ? '&#013;&#010;' : '';
							$alefbet = unicoder_array(
								$from='5d0',
								$to='5ea',
								$include=array(),
								$exclude=array()
							);
							$nikud = unicoder_array(
								$from='',
								$to='',
								$include=array('5b0','5b1','5b2','5b3','5b4','5b5','5b6','5b7','5b8','5b9','5bb','5bc'),
								$exclude=array(),
								$order=''
							);
							$i=0;
							foreach ($alefbet as $alefbet_item) {
								$sofiyot = array('5da','5dd','5df','5e3','5e5');
								if(!in_array($alefbet_item, $sofiyot)){ //not sofiyot
									foreach ($nikud as $nikud_item) {
										echo unicoder_output($alefbet_item);
										echo unicoder_output($nikud_item);
										echo $spaces;
									}
									if($dagesh!='dagesh'){
										foreach ($nikud as $nikud_item) { //dgeshim
											if($nikud_item != '5bc'){ // no dagesh twice
												echo unicoder_output($alefbet_item);
												echo unicoder_output('5bc');
												echo unicoder_output($nikud_item);
												echo $spaces;
											}
										}
									}
									echo $breakline_break;
								}
								if($alefbet_item== '5db'){ //kaf sofit
									echo unicoder_output('5da');
									echo unicoder_output('5b0');
									echo $spaces;
									echo unicoder_output('5da');
									echo unicoder_output('5b8');
									echo $spaces;
									echo unicoder_output('5da');
									echo unicoder_output('5bc');
									echo $breakline_break;
									echo $spaces;
								}

								if($alefbet_item== '5e9'){ //shin
									if($nikud_item != '5bc'){ // no dagesh twice
										foreach ($nikud as $nikud_item) {
											echo unicoder_output('5e9');
											echo unicoder_output('5bc');
											echo unicoder_output('5c1');
											echo unicoder_output($nikud_item);
											echo $spaces;
										}
									}
									foreach ($nikud as $nikud_item) {
										if($nikud_item != '5bc'){ // no dagesh twice
											echo unicoder_output('5e9');
											echo unicoder_output('5bc');
											echo unicoder_output('5c2');
											echo unicoder_output($nikud_item);
											echo $spaces;
										}
									}
									echo $breakline_break;
								}

							}
						} ?>

						<div class="full-box big-text">
							<textarea name="output" class="result"><?php
							if (isset($_POST['submit'])){ //sent
								nikud_unicoder($spaces, $dagesh, $breakline);
							} ?></textarea>
						</div>

					</form>
					<small></small>

					<?php break;

	/////////////////////////////////////////////////////////////////////////////////////////////////////////
				case 'mikud': ?>
					<div class="info">
						<p>גלו מה המיקוד שלכם.</p>
					</div>

					<?php
					$city = isset($_GET["city"]) ? stripslashes(stripslashes($_POST['city'])) : '';
					$city = isset($_GET["city"]) ? stripslashes(stripslashes($_POST['city'])) : '';
					$street = isset($_GET["street"]) ? stripslashes(stripslashes($_POST['street'])) : '';
					$house = isset($_GET["house"]) ? stripslashes(stripslashes($_POST['house'])) : '';
					$pob = isset($_GET["pob"]) ? stripslashes(stripslashes($_POST['pob'])) : '';
					?>
					<form method="post" action="/resources/hameholel?a=<?=$a?>">
						<input type="hidden" value="mikud" name="a">
						<?php
						if (isset($_POST['submit'])) : //sent
							$content = file_get_contents('http://www.israelpost.co.il/zip_data.nsf/SearchZip?OpenAgent&Location='.urlencode($city).'&POB='.urlencode($pob).'&Street='.urlencode($street).'&House='.urlencode($house).'&Entrance='.urlencode($entrance));
							$content = str_replace (array("\r\n", "\n", "\r","-"), ' ', $content);
						endif;
						?>
						<div class="settings settings-cols">
							<div class="options">
								<div>
									<label for="city">עיר: </label>
									<input type="text" name="city" id="city" value="<?=$city?>" placeholder="">
								</div>
								<div>
									<label for="street">רחוב: </label>
									<input type="text" name="street" id="street" value="<?=$street?>" placeholder="">
								</div>
								<div>
									<label for="house">בית: </label>
									<input type="number" name="house" id="house" value="<?=$house?>">
								</div>
								<div>
									<label for="pob">או: ת.ד: </label>
									<input type="number" name="pob" id="pob" value="<?=$pob?>">
								</div>
							</div>
							<input type="submit" class="middle" value="<?=(!isset($_POST['submit']))?'חולל!':'חולל בשנית!'?>" name="submit">
						</div>
						<div class="full-box small-text">
							<textarea name="output" class="result"><?php
							if (isset($_POST['submit'])) : //sent
								$mikud = strip_tags($content);
								$mikud = str_replace(' ', '', $mikud);
								$mikud = mb_substr($mikud, 4, 7, 'utf-8'); //RES89751886
								if(strlen($mikud)==7 && is_numeric($mikud)) echo $mikud;
								else echo 'יבשנית או רחוב לא קיים';
							endif;
							?></textarea>
						</div>

					</form>
					<small>ט.ל.ח.</small>
					<?php break;

			} //end switch ($a) ?>

		</section><!-- /.content -->
	</div><!-- /.cont-wrap -->
</section><!--/.archive-wrap-->
