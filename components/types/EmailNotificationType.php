<?php namespace app\components\types; 
                class EmailNotificationType extends NotificationType
                {
                    function doExecute()
                    {
                        
                        $this->sendMail($this->from->email, $this->to->email, $this->title, $this->text);
    
                    }
            }