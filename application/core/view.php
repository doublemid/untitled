<?php

class View
{
    public $data = array();
    public $buffer;

    private function Clear()
    {
        $this->buffer = "";
    }

    private function Read()
    {
        $tmp = $this->buffer;
        $this->Clear();
        return $tmp;
    }

    private function Show()
    {
       echo $this->Read();
    }

    private function Parse()
    {
        foreach($this->data as $key=>$value){
            $this->buffer = str_replace('{' . $key . '}', $value, $this->buffer);
        }
    }

    private function PrepareView($filename)
    {
        if (file_exists("application/views/" . $filename)) {
            $this->buffer .= join('', file("application/views/" . $filename));
        }
    }

    public function generate($views)
    {

        foreach ($views as $key => $value) {
            $this->PrepareView($value);
        }
        $this->Parse();
        $this->Show();

    }
    
}
