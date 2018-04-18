<?php

declare(strict_types=1);

namespace Doctrine\Website\DoctrineSculpinBundle\Command;

use Sculpin\Core\Console\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Yaml\Yaml;
use function array_merge;
use function file_get_contents;
use function file_put_contents;
use function json_decode;
use function shell_exec;
use function sprintf;

class GenerateTeamMembersCommand extends ContainerAwareCommand
{
    /** @var string */
    private $username;

    /** @var string */
    private $password;

    protected function configure() : void
    {
        $this
            ->setName('generate-team-members')
            ->setDescription('Generate team members from GitHub API.')
        ;
    }

    protected function initialize(InputInterface $input, OutputInterface $output) : void
    {
        $helper = $this->getHelper('question');

        $githubUsername = new Question('GitHub Username: ');

        $this->username = $helper->ask($input, $output, $githubUsername);

        $githubPassword = new Question('GitHub Password: ');

        $this->password = $helper->ask($input, $output, $githubPassword);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $githubTeamMembers = $this->getTeamMembersFromGitHub();

        $path = $this->getContainer()->getParameter('kernel.root_dir') . '/config/team_members.yml';

        $data = Yaml::parse(file_get_contents($path));

        $teamMembers = [];

        foreach ($githubTeamMembers as $githubTeamMember) {
            $githubTeamMemberInfo = $this->getTeamMemberFromGitHub(
                $githubTeamMember['login']
            );

            $teamMembers[$githubTeamMember['login']] = [
                'name' => $githubTeamMemberInfo['name'],
                'github' => $githubTeamMember['login'],
                'avatarUrl' => $githubTeamMember['avatar_url'],
                'website' => $githubTeamMemberInfo['blog'],
                'location' => $githubTeamMemberInfo['location'],
            ];
        }

        foreach ($data['parameters']['doctrine.team_members'] as $key => $teamMember) {
            $teamMembers[$key] = array_merge($teamMember, $teamMembers[$key]);
        }

        $data['parameters']['doctrine.team_members'] = $teamMembers;

        file_put_contents($path, Yaml::dump($data, 4));
    }

    private function getTeamMembersFromGitHub() : array
    {
        $json = shell_exec(sprintf(
            'curl -u %s:%s https://api.github.com/orgs/doctrine/members?per_page=100&page=1',
            $this->username,
            $this->password
        ));

        return json_decode($json, true);
    }

    private function getTeamMemberFromGitHub(string $login) : array
    {
        $json = shell_exec(sprintf(
            'curl -u %s:%s https://api.github.com/users/%s',
            $this->username,
            $this->password,
            $login
        ));

        return json_decode($json, true);
    }
}
