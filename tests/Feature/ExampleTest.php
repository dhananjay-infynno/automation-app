<?php

test('the application redirects to todos', function () {
    $this->get('/')
        ->assertRedirect('/todos');
});
