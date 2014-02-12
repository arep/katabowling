<?php
require "Frames.php";

$bowling = new Bowling("XXXXXXXXXXXX");
echo $bowling->calculateTotalScore()."\n"; //300

$bowling = new Bowling("9-9-9-9-9-9-9-9-9-9-");
echo $bowling->calculateTotalScore()."\n"; //90

$bowling = new Bowling("5/5/5/5/5/5/5/5/5/5/5");
echo $bowling->calculateTotalScore()."\n"; //150

$bowling = new Bowling("5/5/5/5/5/5/5/5/5/5/2");
echo $bowling->calculateTotalScore()."\n"; //147


class Bowling {

    /**
     * Array with structure:
     * 0 -> "X",
     * 1 -> "25",
     * 2 -> "5-",
     * 3 -> "2/",
     * etc
     *
     * @var array
     */
    protected $scoring_sequence_arr;
    protected $bonus_throws = '';

    public function __construct($scoring_sequence){
        //Here we build an array with each frame (bowling turn) result
        $seq = str_split($scoring_sequence);
        for ($i=0; $i < count($seq); $i++){
            $char = $seq[$i];
            //There is only 10 frames. The rest of the sequence is bonus throws.
            if (count($this->scoring_sequence_arr)==10){
                $this->bonus_throws .= $char;
                continue;
            }
            if ($char == "X"){
                $this->scoring_sequence_arr[]=$char;
                continue;
            }

            $this->scoring_sequence_arr[] = $seq[$i] . $seq[$i+1];

            $i++;
        }
    }

    public function calculateTotalScore(){
        $frame = new FrameNull();
        foreach ($this->scoring_sequence_arr as $frame_sequence){
            $frame = FrameFactory::factory($frame_sequence, /* previous frame */ $frame);
            //Go back and calculate for the previous frame now that we know the frame after it
            $frame->getPreviousFrame()->calculateScore($frame);
        }
        //bonus throws is only used to calculate the last frame score
        //Therefore we create a "stand alone" frame for this here and use it as the base for the last frame.
        $frame->calculateScore(FrameFactory::factory($this->bonus_throws, new FrameNull()));

        $total_score = 0;
        //Loop back to the start while summing up all the scores
        while (!($frame instanceof FrameNull)){
            $score = $frame->getScore();
            error_log(get_class($frame).":".$score);
            $total_score = $total_score + $score;
            $frame = $frame->getPreviousFrame();
        }
        return $total_score;
    }
}
