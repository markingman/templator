<?php

return function (bool $a, string $b, array $c): ?array {
	return ($a and $b === 'B' and $c === [1]) ? [1, 2, 3] : null;
};
