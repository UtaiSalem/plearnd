<?php

namespace App\Constants;

class QuizConstants
{
    const SCORE_RATIO_WEIGHT = 35;
    const TIME_EFFICIENCY_WEIGHT = 25;
    const ACCURACY_WEIGHT = 25;
    const EDIT_PENALTY_WEIGHT = 15;
    
    const MAX_PENALTY = 100;
    const STATUS_PASSED = 2;
    const STATUS_FAILED = 0;
    const FAILING_STATUS = 0;
}
