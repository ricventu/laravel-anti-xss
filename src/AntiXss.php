<?php

declare(strict_types=1);

namespace Ricventu\LaravelAntiXss;

use voku\helper\AntiXSS as VokuAntiXSS;

class AntiXss
{
    protected VokuAntiXSS $engine;

    public function __construct(array $config = [])
    {
        $this->engine = new VokuAntiXSS;
        $this->applyConfig($config);
    }

    public function clean(string|array $input): string|array
    {
        return $this->engine->xss_clean($input);
    }

    public function isXssFound(): ?bool
    {
        return $this->engine->isXssFound();
    }

    public function contains(string $input): bool
    {
        $clone = clone $this->engine;
        $clone->xss_clean($input);

        return $clone->isXssFound() === true;
    }

    public function engine(): VokuAntiXSS
    {
        return $this->engine;
    }

    public function setReplacement(string $replacement): self
    {
        $this->engine->setReplacement($replacement);

        return $this;
    }

    public function setKeepPreAndCodeTagContent(bool $bool): self
    {
        $this->engine->setKeepPreAndCodeTagContent($bool);

        return $this;
    }

    public function setStripe4byteChars(bool $bool): self
    {
        $this->engine->setStripe4byteChars($bool);

        return $this;
    }

    public function addEvilAttributes(array $attrs): self
    {
        $this->engine->addEvilAttributes($attrs);

        return $this;
    }

    public function removeEvilAttributes(array $attrs): self
    {
        $this->engine->removeEvilAttributes($attrs);

        return $this;
    }

    public function addEvilHtmlTags(array $tags): self
    {
        $this->engine->addEvilHtmlTags($tags);

        return $this;
    }

    public function removeEvilHtmlTags(array $tags): self
    {
        $this->engine->removeEvilHtmlTags($tags);

        return $this;
    }

    protected function applyConfig(array $config): void
    {
        if (isset($config['replacement']) && is_string($config['replacement'])) {
            $this->engine->setReplacement($config['replacement']);
        }

        if (array_key_exists('keep_pre_and_code_tag_content', $config)) {
            $this->engine->setKeepPreAndCodeTagContent((bool) $config['keep_pre_and_code_tag_content']);
        }

        if (array_key_exists('strip_4byte_chars', $config)) {
            $this->engine->setStripe4byteChars((bool) $config['strip_4byte_chars']);
        }

        $evilAttributes = $config['evil_attributes'] ?? [];
        if (! empty($evilAttributes['add'])) {
            $this->engine->addEvilAttributes((array) $evilAttributes['add']);
        }
        if (! empty($evilAttributes['remove'])) {
            $this->engine->removeEvilAttributes((array) $evilAttributes['remove']);
        }

        $evilTags = $config['evil_html_tags'] ?? [];
        if (! empty($evilTags['add'])) {
            $this->engine->addEvilHtmlTags((array) $evilTags['add']);
        }
        if (! empty($evilTags['remove'])) {
            $this->engine->removeEvilHtmlTags((array) $evilTags['remove']);
        }
    }
}
