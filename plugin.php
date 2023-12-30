<?php

class pluginUnderConstructionOrMaintenance extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'enable'=>false,
			'allowLoggedInUser'=>true,
			'allowedUserRoles'=>'admin', // all => For everyone, <role name> => comma seperated values
			'modeType'=>'underconstruction_bg.jpg',
			'title'=>explode("</title>",explode("<title>", Theme::metaTags('title'))[1])[0],
			'message'=>'We are cooking something awesome!',
			'subMessage'=>'We are expecting to become available by tomorrow.',
			'contactEmail'=>'youremail@site.com',
			'contactNumber'=>'+XX XXXX XXXX XX'
		);
		if (!isset($login)) {
            $login = new Login();
        }
	}

	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div><div class="row">';
		
		$html .= '<div class="col col-lg-6 col-12">';
		$html .= '<label>'.$L->get('Are you under something?').'</label>';
		$html .= '<select name="enable">';
		$html .= '<option value="true" '.($this->getValue('enable')===true?'selected':'').'>Yeah, I am working on website.</option>';
		$html .= '<option value="false" '.($this->getValue('enable')===false?'selected':'').'>Nope! All good.</option>';
		$html .= '</select>';
		$html .= '</div>';
		
		$html .= '<div class="col col-lg-6 col-12">';
		$html .= '<label>'.$L->get('What you want to see in background?').'</label>';
        $html .= '<select name="modeType">';
        $html .= '<option value="underconstruction_bg.jpg" '.($this->getValue('modeType')==='underconstruction_bg.jpg'?'selected':'').'>'.$L->get('Under Construction').'</option>';
        $html .= '<option value="maintenance_bg.jpg" '.($this->getValue('modeType')==='maintenance_bg.jpg'?'selected':'').'>'.$L->get('Under Maintenance').'</option>';
        $html .= '<option value="comingsoon_bg.jpg" '.($this->getValue('modeType')==='comingsoon_bg.jpg'?'selected':'').'>'.$L->get('Coming Soon').'</option>';
        $html .= '<option value="closed_bg.jpg" '.($this->getValue('modeType')==='closed_bg.jpg'?'selected':'').'>'.$L->get('We\'re Closed').'</option>';
        $html .= '</select>';
        $html .= '</div>';

		$html .= '<div class="col col-lg-6 col-12">';
		$html .= '<label>'.$L->get('Can logged in user see the website?').'</label>';
		$html .= '<select id="ucm_allowLoggedInUser" name="allowLoggedInUser">';
		$html .= '<option value="true" '.($this->getValue('allowLoggedInUser')===true?'selected':'').'>Yes, they must.</option>';
		$html .= '<option value="false" '.($this->getValue('allowLoggedInUser')===false?'selected':'').'>Nooo, let\'s hide it for everyone even logged in users.</option>';
		$html .= '</select>';
		$html .= '</div>';

		$html .= '<div class="col col-lg-6 col-12">';
		$html .= '<label>'.$L->get('For what user role?').'</label>';
		$html .= '<select id="ucm_allowedUserRoles" name="allowedUserRoles" '. ($this->getValue('allowLoggedInUser') ? '' : 'disabled') .'>';
		$html .= '<option value="all" '.($this->getValue('allowedUserRoles')==='all'?'selected':'').'>All logged in users.</option>';
		$html .= '<option value="admin" '.($this->getValue('allowedUserRoles')==='admin'?'selected':'').'>Only Admins.</option>';
		$html .= '<option value="admin,editor" '.($this->getValue('allowedUserRoles')==='admin,editor'?'selected':'').'>Admins and Editors</option>';
		$html .= '<option value="admin,editor,author" '.($this->getValue('allowedUserRoles')==='admin,editor,author'?'selected':'').'>Admins, Editors and Authors</option>';
		$html .= '</select>';
		$html .= '</div>';
        
        $html .= '</div></div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('Title').' *</label>';
		$html .= '<input name="title" id="jstitle" type="text" value="'.$this->getValue('title').'" required placeholder="'.explode("</title>",explode("<title>", Theme::metaTags('title'))[1])[0].'">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('Message').' *</label>';
		$html .= '<input name="message" id="jsmessage" type="text" value="'.$this->getValue('message').'" required placeholder="What you want to tell to world? (Required)">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('Sub Message').'</label>';
		$html .= '<input name="subMessage" id="jssubMessage" type="text" value="'.$this->getValue('subMessage').'" placeholder="Moto of your website (optional)">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('Contact Email').'</label>';
		$html .= '<input name="contactEmail" id="jscontactEmail" type="text" value="'.$this->getValue('contactEmail').'" placeholder="Contact email (optional) e.g., youremail@site.com">';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('Contact Number').'</label>';
		$html .= '<input name="contactNumber" id="jscontactNumber" type="text" value="'.$this->getValue('contactNumber').'" placeholder="Contact number (optional) e.g., +XX XXXX XXXX XX">';
		$html .= '</div>';
		
		$html .= '<script>';
		$html .= 'const ucm_allowLoggedInUser = document.getElementById("ucm_allowLoggedInUser"), ucm_allowedUserRoles = document.getElementById("ucm_allowedUserRoles");';
		$html .= 'ucm_allowLoggedInUser.addEventListener("change", event => {if(event.target.value=="true"){ucm_allowedUserRoles.removeAttribute("disabled");} else {ucm_allowedUserRoles.setAttribute("disabled", true);}});';
		$html .= '</script>';

		return $html;
	}

	public function beforeAll()
	{
	    global $L, $login;
	    if (!isset($login)) {
            $login = new Login();
        }
		if ($this->getValue('enable') && !($this->getValue('allowLoggedInUser') && $login->isLogged() 
		        && ($this->getValue('allowedUserRoles') === 'all' || str_contains($this->getValue('allowedUserRoles'), $login->role())))) {
		    $html  = '<!DOCTYPE html><html lang="en"><head>';
		    
		    $html .= '<title>'.$this->getValue('title').'</title>';
		    
		    $html .= '<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" />'.PHP_EOL;
		    $html .= '<link rel="stylesheet" href="' . HTML_PATH_PLUGINS . '01under-construction-maintenance/css/style.css">'.PHP_EOL;
		    $html .= '<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" /><link href="https://fonts.googleapis.com/css?family=Grand+Hotel" rel="stylesheet" type="text/css" />';
		    
		    $html .= '</head>';
		    
		    $html .= '<body data-new-gr-c-s-loaded="8.878.0">';
		    
		    $html .= '<div class="main" style="background-image: url(\'' . HTML_PATH_PLUGINS . '01under-construction-maintenance/images/' . $this->getValue('modeType') . '\')">';
            $html .= '<div class="cover black" data-color="black"></div>';
            $html .= '<div class="container">';
            $html .= '<h1 class="logo cursive">' . $this->getValue('message') . '</h1>';
            
            $html .= '<div class="content">';
            if ($this->getValue('subMessage') !== '') {
                $html .= '<h4 class="motto">' . $this->getValue('subMessage') . '</h4>';
            }
            
            $html .= '<div class="subscribe">';
            if ($this->getValue('contactEmail') !== '' || $this->getValue('contactNumber') !== '') {
                $html .= '<p class="info-text">Contact Us';
                if ($this->getValue('contactEmail') !== '') {
                    $html .= '<br>Contact Email: ' . $this->getValue('contactEmail');
                }
                if ($this->getValue('contactNumber') !== '') {
                    $html .= '<br>Contact Number: ' . $this->getValue('contactNumber');
                }
                $html .= '</p>';
            }
            $html .= '</div></div></div></div><script src="https://code.jquery.com/jquery-1.12.4.min.js" type="text/javascript"></script><script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" type="text/javascript"></script></body>';
            

            
		    $html .= '</body>';
			exit( $html );

		}
	}
}