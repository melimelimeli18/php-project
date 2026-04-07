<?php
// routes/ai.php

use App\Mcp\Servers\SentosaQuiz;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp/sentosa-quiz', SentosaQuiz::class);