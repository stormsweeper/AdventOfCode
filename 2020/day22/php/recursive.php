<?php

$input = trim(file_get_contents($argv[1]));
list($deck1, $deck2) = explode("\n\n", $input);
$deck1 = array_map('intval', explode("\n", substr($deck1, 10)));
$deck2 = array_map('intval', explode("\n", substr($deck2, 10)));

class CrabCombat {
    private array $prev_decks = [];
    private int $winner = 0;
    private int $score = 0;
    function __construct(private array $deck1, private array $deck2) {}
    function winner(): int { return $this->winner; }
    function score(): int {
        if (empty($this->winner)) return 0;

        if (empty($this->score)) {
            if ($this->winner === 1) {
                $deck = $this->deck1;
            } else {
                $deck = $this->deck2;
            }
            $score = 0;
            $size = count($deck);
            foreach ($deck as $i => $card) {
                $score += $card * ($size - $i);
            }
            $this->score = $score;
        }

        return $this->score;
    }

    function play(): void {
        while (!$this->winner) {
            $this->takeTurn();
        }
    }

    private function takeTurn(): void {
        // check decks
        if (!$this->checkDecks()) {
            $this->winner = 1;
            return;
        }

        // take top cards
        $card1 = array_shift($this->deck1);
        $card2 = array_shift($this->deck2);

        $round_winner = 0;
        // maybe recurse
        if (
            $card1 <= count($this->deck1)
            &&
            $card2 <= count($this->deck2)
        ) {
            $sub_deck1 = array_slice($this->deck1, 0, $card1);
            $sub_deck2 = array_slice($this->deck2, 0, $card2);
            $sub_game = new CrabCombat($sub_deck1, $sub_deck2);
            $sub_game->play();
            $round_winner = $sub_game->winner();
        }
        // else normal comparison
        elseif ($card1 > $card2) {
            $round_winner = 1;
        } else {
            $round_winner = 2;
        }

        if (!$round_winner) throw new RuntimeException('How?');

        // put cards away
        if ($round_winner === 1) {
            $this->deck1[] = $card1;
            $this->deck1[] = $card2;
        } else {
            $this->deck2[] = $card2;
            $this->deck2[] = $card1;
        }

        // check for winner
        if (!$this->deck2) {
            $this->winner = 1;
        } elseif (!$this->deck1) {
            $this->winner = 2;
        }
    }

    private function checkDecks(): bool {
        $key = sprintf(
            '%s;%s',
            implode(',', $this->deck1),
            implode(',', $this->deck2),
        );

        if (isset($this->prev_decks[$key])) return false;

        $this->prev_decks[$key] = 1;
        return true;
    }
}

$main_game = new CrabCombat($deck1, $deck2);
$main_game->play();
echo $main_game->score();