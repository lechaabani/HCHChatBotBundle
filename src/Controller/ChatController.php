namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use HCH\ChatBotBundle\Service\ChatBotService;

class ChatController extends AbstractController
{
    public function chat(ChatBotService $chatBot): Response
    {
        $response = $chatBot->sendMessage('Bonjour!');
        
        return $this->json(['response' => $response]);
    }
} 