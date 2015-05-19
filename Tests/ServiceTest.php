<?php

/*
 * This file is part of the gnugat/marshaller-bundle package.
 *
 * (c) Loïc Chardonnet <loic.chardonnet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gnugat\MarshallerBundle\Tests;

use Gnugat\MarshallerBundle\Tests\Fixtures\Article;
use PHPUnit_Framework_TestCase;

class ServiceTest extends PHPUnit_Framework_TestCase
{
    const TITLE = 'Nobody expects...';
    const CONTENT = '... The Spanish Inquisition!';

    private $marshaller;

    protected function setUp()
    {
        $kernel = new \AppKernel('test', false);
        $kernel->boot();

        $this->marshaller = $kernel->getContainer()->get('gnugat_marshaller.marshaller');
    }

    /**
     * @test
     */
    public function it_converts_article_to_array()
    {
        $article = Article::draft(self::TITLE, self::CONTENT);

        $expected = array(
            'title' => self::TITLE,
            'content' => self::CONTENT,
        );
        self::assertSame($expected, $this->marshaller->marshal($article));
    }

    /**
     * @test
     */
    public function it_partially_converts_article_to_array()
    {
        $article = Article::draft(self::TITLE, self::CONTENT);

        $expected = array(
            'title' => self::TITLE,
        );
        self::assertSame($expected, $this->marshaller->marshal($article, 'partial'));
    }
}
