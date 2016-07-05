<?php namespace app\components\types; 
                class BrowserNotificationType extends NotificationType
                {
                    function doExecute()
                    {
                        
                        $this->saveToDb($this->from, $this->to, $this->title, $this->text);
                    }
            }