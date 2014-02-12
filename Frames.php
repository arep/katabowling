<?php

interface FrameInterface {
    public function getScore();
    public function getFirst();
    public function getSecond();
    public function calculateScore(FrameInterface $future_frame_score);
}

abstract class AbstractFrame implements FrameInterface {
    protected $previous_frame_score;
    protected $first_throw;
    protected $second_throw;

    public function __construct(FrameInterface $previous_frame_score, $first_throw = null, $second_throw = null){
        $this->previous_frame_score = $previous_frame_score;
        $this->first_throw = $first_throw;
        $this->second_throw = $second_throw;
    }

    public function getFirst(){
        return $this->first_throw;
    }

    public function getSecond(){
        return $this->second_throw;
    }

    public function getPreviousFrame(){
        return $this->previous_frame_score;
    }

    public function calculateScore(FrameInterface $future_frame_score){
        //Override if class needs to do special score calculation
    }

}


class FrameFactory {

    public static function factory($sequence, FrameInterface $previous_frame_score) {
        //$sequence is one or two chars long
        $first_throw = substr($sequence,0,1);
        if (strlen($sequence) > 1){
            $second_throw = substr($sequence,1,1);
        }else{
            $second_throw = 0;
        }

        if ($first_throw == "X"){
            return new FrameStrike($previous_frame_score);
        }elseif($first_throw == "-"){
            $first_throw = 0;
        }

        if ($second_throw == "/"){
            return new FrameSpare($previous_frame_score, $first_throw);
        }
        if ($second_throw == "-"){
            $second_throw = 0;
        }
        return new FrameNormal($previous_frame_score, $first_throw, $second_throw);

    }
}

class FrameNormal extends AbstractFrame {

    public function getScore(){
        return $this->first_throw + $this->second_throw;
    }

}

class FrameSpare extends AbstractFrame {
    protected $score = 10;

    public function getScore(){
        return $this->score;
    }

    public function calculateScore(FrameInterface $future_frame_score){
        if ($future_frame_score instanceof FrameStrike){
            $score = 10;
        }elseif($future_frame_score instanceof FrameSpare){
            $score = $future_frame_score->getFirst();
        }else{
            $score = $future_frame_score->getFirst();
        }
        $this->score = $this->score + $score;
    }
}

class FrameStrike extends AbstractFrame {
    protected $score = 10;
    protected $first_throw = 10;
    protected $second_throw = 10;

    public function getScore(){
        return $this->score;
    }

    public function calculateScore(FrameInterface $future_frame_score){
        if ($future_frame_score instanceof FrameStrike){
            $score = 20;
        }elseif($future_frame_score instanceof FrameSpare){
            $score = $future_frame_score->getFirst() + 10;
        }else{
            $score = $future_frame_score->getScore();
        }
        $this->score = $this->score + $score;
    }
}

/**
 * Class FrameNull
 * Start and ending frame. Does not count on score
 */
class FrameNull extends AbstractFrame {
    public function __construct(){

    }

    public function getScore(){
        return 0;
    }


}


