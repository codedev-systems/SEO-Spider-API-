<?php
    class Robots {
        private $rules = [];

        public function __construct($content) {
            $this->parse($content);
        }

        private function parse($content) {
            $lines = explode("\n", $content);
            $currentAgents = [];

            foreach ($lines as $line) {
                $line = trim($line);

                // Ignora comentários e linhas vazias
                if ($line === '' || str_starts_with($line, '#')) {
                    continue;
                }

                if (strpos($line, ':') !== false) {
                    list($directive, $value) = explode(':', $line, 2);
                    $directive = strtolower(trim($directive));
                    $value = trim($value);

                    if ($directive === 'user-agent') {
                        $currentAgents = [$value];
                        if (!isset($this->rules[$value])) {
                            $this->rules[$value] = [];
                        }
                    } elseif (in_array($directive, ['allow', 'disallow'])) {
                        foreach ($currentAgents as $agent) {
                            $this->rules[$agent][] = [
                                'type' => $directive,
                                'pattern' => $this->convertPattern($value),
                                'raw' => $value
                            ];
                        }
                    }
                }
            }
        }

        private function convertPattern($pattern) {
            // Escapa regex
            $pattern = preg_quote($pattern, '/');

            // Converte * em regex
            $pattern = str_replace('\*', '.*', $pattern);

            // Converte $ para fim de string
            if (str_ends_with($pattern, '\$')) {
                $pattern = substr($pattern, 0, -2) . '$';
            } else {
                $pattern .= '.*';
            }

            return '/^' . $pattern . '/i';
        }

        public function isAllowed($url, $userAgent = '*') {
            $path = parse_url($url, PHP_URL_PATH) ?? '/';

            // Pega regras do user-agent específico, senão usa *
            $rules = $this->rules[$userAgent] ?? $this->rules['*'] ?? [];

            $matchedRule = null;
            foreach ($rules as $rule) {
                if (preg_match($rule['pattern'], $path)) {
                    // Mantém a regra mais específica (maior string raw)
                    if ($matchedRule === null || strlen($rule['raw']) > strlen($matchedRule['raw'])) {
                        $matchedRule = $rule;
                    }
                }
            }

            if ($matchedRule === null) {
                return true; // sem regras → permitido
            }

            return $matchedRule['type'] === 'allow';
        }

        public function getRules() {
            return $this->rules;
        }
    }
?>


