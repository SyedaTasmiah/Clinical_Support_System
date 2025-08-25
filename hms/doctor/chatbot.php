<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('include/config.php');

// Check if doctor is logged in
if(!isset($_SESSION['id']) || strlen($_SESSION['id']) == 0) {
    header('location:logout.php');
    exit();
}

$doctor_id = $_SESSION['id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Doctor | Chatbot</title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    
    <style>
        .chat-container {
            height: 550px;
            border: none;
            border-radius: 15px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .chat-header {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }
        .chat-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }
        .chat-header h4 {
            margin: 0;
            font-size: 1.4em;
            font-weight: 600;
            position: relative;
            z-index: 1;
            color: #ffffff !important;
            text-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }
        .chat-header small {
            opacity: 0.95;
            font-size: 0.9em;
            position: relative;
            z-index: 1;
            color: #ffffff !important;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }
        .chat-messages {
            height: 380px;
            overflow-y: auto;
            padding: 20px;
            background: #ffffff;
        }
        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }
        .chat-messages::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        .chat-messages::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }
        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        .message {
            margin-bottom: 20px;
            display: flex;
            animation: fadeInUp 0.3s ease-out;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .message.user {
            justify-content: flex-end;
        }
        .message.bot {
            justify-content: flex-start;
        }
        .message-content {
            max-width: 75%;
            padding: 15px 20px;
            border-radius: 20px;
            word-wrap: break-word;
            position: relative;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .message.user .message-content {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            color: white;
            border-bottom-right-radius: 5px;
        }
        .message.user .message-content::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: -8px;
            width: 0;
            height: 0;
            border: 8px solid transparent;
            border-left-color: #1e7e34;
            border-bottom: none;
        }
        .message.bot .message-content {
            background: white;
            color: #333;
            border: 1px solid #e9ecef;
            border-bottom-left-radius: 5px;
        }
        .message.bot .message-content::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: -8px;
            width: 0;
            height: 0;
            border: 8px solid transparent;
            border-right-color: #e9ecef;
            border-bottom: none;
        }
        .chat-input {
            padding: 20px;
            border-top: 1px solid #e9ecef;
            background: white;
            border-radius: 0 0 15px 15px;
        }
        .chat-input input {
            border-radius: 25px;
            border: 2px solid #e9ecef;
            padding: 12px 20px;
            font-size: 14px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .chat-input input:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 3px rgba(40,167,69,0.1);
            outline: none;
        }
        .chat-input button {
            border-radius: 25px;
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            border: none;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(40,167,69,0.3);
        }
        .chat-input button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40,167,69,0.4);
        }
        .typing-indicator {
            display: none;
            color: #6c757d;
            font-style: italic;
            margin: 0 20px 20px 20px;
            padding: 10px 15px;
            background: #f8f9fa;
            border-radius: 15px;
            border-left: 3px solid #28a745;
        }
        .quick-actions {
            margin-top: 25px;
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .quick-actions h5 {
            color: #495057;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .quick-actions .btn {
            margin: 5px;
            border-radius: 20px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 2px solid #28a745;
            background: white;
            color: #28a745;
        }
        .quick-actions .btn:hover {
            background: #28a745;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(40,167,69,0.3);
        }
        .model-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #28a745;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .model-details {
            font-size: 0.9em;
            color: #495057;
            line-height: 1.4;
        }
        .model-selector {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .model-selector select {
            border-radius: 8px;
            border: 2px solid #28a745;
            padding: 10px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .model-selector select:focus {
            border-color: #1e7e34;
            box-shadow: 0 0 0 3px rgba(40,167,69,0.1);
            outline: none;
        }
        .model-selector label {
            color: #495057;
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
        }
        .panel {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        }
        .panel-heading {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 15px 15px 0 0;
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        .panel-title {
            color: #495057;
            font-weight: 600;
            margin: 0;
        }
        .panel-body {
            padding: 25px;
        }
        .mainTitle {
            color: #495057;
            font-weight: 600;
        }
        .mainTitle i {
            color: #28a745;
            margin-right: 10px;
        }
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
        }
        .breadcrumb li {
            color: #6c757d;
        }
        .breadcrumb li.active {
            color: #28a745;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div id="app">
        <?php include('include/sidebar.php');?>
        <div class="app-content">
            <?php include('include/header.php');?>
            <div class="main-content">
                <div class="wrap-content container" id="container">
                    <section id="page-title">
                        <div class="row">
                            <div class="col-sm-8">
                                <h1 class="mainTitle">
                                    <i class="fa fa-robot"></i> AI Medical Assistant
                                </h1>
                            </div>
                            <ol class="breadcrumb">
                                <li>
                                    <span>Doctor</span>
                                </li>
                                <li class="active">
                                    <span>Chatbot</span>
                                </li>
                            </ol>
                        </div>
                    </section>

                    <div class="container-fluid container-fullw bg-white">
                        <div class="row">
                            <div class="col-md-8 col-md-offset-2">
                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">Medical AI Assistant for Doctors</h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="chat-container">
                                            <div class="chat-header">
                                                <h4><i class="fa fa-user-md"></i> Medical AI Assistant</h4>
                                                <small>Your AI-powered medical assistant for patient care and medical queries</small>
                                            </div>
                                            
                                            <div class="chat-messages" id="chatMessages">
                                                <div class="message bot">
                                                    <div class="message-content">
                                                        Hello Doctor! I'm your AI medical assistant. I can help you with:
                                                        <br>• Patient management
                                                        <br>• Medical guidelines and protocols
                                                        <br>• Appointment scheduling
                                                        <br>• Medical research and references
                                                        <br>• Administrative tasks
                                                        <br><br>How can I assist you today?
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="typing-indicator" id="typingIndicator">
                                                AI is analyzing your query...
                                            </div>
                                            
                                            <div class="chat-input">
                                                <form id="chatForm" class="form-inline">
                                                    <div class="form-group" style="width: 85%;">
                                                        <input type="text" class="form-control" id="messageInput" 
                                                               placeholder="Type your medical query here..." style="width: 100%;">
                                                    </div>
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="fa fa-paper-plane"></i> Send
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        
                                        <div class="quick-actions">
                                            <h5>Quick Actions:</h5>
                                            <button class="btn btn-outline-success btn-sm quick-action" data-action="patients">Patient Management</button>
                                            <button class="btn btn-outline-success btn-sm quick-action" data-action="appointments">Appointment Schedule</button>
                                            <button class="btn btn-outline-success btn-sm quick-action" data-action="guidelines">Medical Guidelines</button>
                                            <button class="btn btn-outline-success btn-sm quick-action" data-action="research">Medical Research</button>
                                            <button class="btn btn-outline-success btn-sm quick-action" data-action="admin">Administrative Help</button>
                                            <button class="btn btn-outline-success btn-sm quick-action" data-action="protocols">Treatment Protocols</button>
                                        </div>
                                        
                                        <div class="row mt-4">
                                            <div class="col-md-6">
                                                <label for="modelSelect" class="form-label"><strong>Select AI Model:</strong></label>
                                                <select class="form-control" id="modelSelect">
                                                    <option value="bioxnet">🤖 BioXNet - Medical AI Assistant</option>
                                                    <option value="hybrid">🧠 Hybrid - Advanced Medical Intelligence</option>
                                                </select>
                                                <small class="text-muted">Choose your preferred AI model for medical assistance</small>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="model-info mt-3">
                                                    <div id="bioxnet-info" class="model-details">
                                                        <strong>BioXNet:</strong> Specialized in patient management and medical protocols
                                                    </div>
                                                    <div id="hybrid-info" class="model-details" style="display: none;">
                                                        <strong>Hybrid:</strong> Advanced AI with comprehensive medical research and diagnostic capabilities
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="vendor/modernizr/modernizr.js"></script>
    <script src="vendor/jquery-cookie/jquery.cookie.js"></script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="vendor/switchery/switchery.min.js"></script>
    <script src="assets/js/main.js"></script>
    
    <script>
        $(document).ready(function() {
            const chatMessages = $('#chatMessages');
            const messageInput = $('#messageInput');
            const chatForm = $('#chatForm');
            const typingIndicator = $('#typingIndicator');
            const modelSelect = $('#modelSelect');
            const bioxnetInfo = $('#bioxnet-info');
            const hybridInfo = $('#hybrid-info');
            
            let currentModel = 'bioxnet';
            
            // Handle model selection change
            modelSelect.on('change', function() {
                currentModel = $(this).val();
                updateModelInfo();
                addModelSwitchMessage();
            });
            
            // Update model info display
            function updateModelInfo() {
                if (currentModel === 'bioxnet') {
                    bioxnetInfo.show();
                    hybridInfo.hide();
                } else {
                    bioxnetInfo.hide();
                    hybridInfo.show();
                }
            }
            
            // Add message when model is switched
            function addModelSwitchMessage() {
                const modelName = currentModel === 'bioxnet' ? 'BioXNet' : 'Hybrid';
                const message = `Switched to ${modelName} model. How can I assist you today, Doctor?`;
                addMessage(message);
            }
            
            // Auto-scroll to bottom
            function scrollToBottom() {
                chatMessages.scrollTop(chatMessages[0].scrollHeight);
            }
            
            // Add message to chat
            function addMessage(message, isUser = false) {
                const messageClass = isUser ? 'user' : 'bot';
                const messageHtml = `
                    <div class="message ${messageClass}">
                        <div class="message-content">
                            ${message}
                        </div>
                    </div>
                `;
                chatMessages.append(messageHtml);
                scrollToBottom();
            }
            
            // Show typing indicator
            function showTyping() {
                typingIndicator.show();
                scrollToBottom();
            }
            
            // Hide typing indicator
            function hideTyping() {
                typingIndicator.hide();
            }
            
            // Simulate AI response based on selected model
            function simulateAIResponse(userMessage) {
                showTyping();
                
                setTimeout(() => {
                    hideTyping();
                    let response = '';
                    
                    const lowerMessage = userMessage.toLowerCase();
                    
                    if (currentModel === 'bioxnet') {
                        response = getBioXNetResponse(lowerMessage);
                    } else {
                        response = getHybridResponse(lowerMessage);
                    }
                    
                    addMessage(response);
                }, 1500);
            }
            
            // BioXNet model responses for doctors
            function getBioXNetResponse(message) {
                if (message.includes('patient') || message.includes('manage')) {
                    return 'I can help you with patient management! You can access patient records, update medical history, and manage prescriptions through the patient management section. Would you like me to guide you to specific patient functions?';
                } else if (message.includes('appointment') || message.includes('schedule')) {
                    return 'For appointment management, you can view your schedule, manage patient appointments, and set availability through the schedule management section. I can help you optimize your appointment workflow.';
                } else if (message.includes('guideline') || message.includes('protocol')) {
                    return 'I can provide information on current medical guidelines and treatment protocols. However, always refer to the latest official medical guidelines and consult with specialists for complex cases. What specific area are you looking for?';
                } else if (message.includes('research') || message.includes('study')) {
                    return 'I can help you find relevant medical research and studies. I can search through medical databases and provide summaries of recent findings. What medical topic are you researching?';
                } else if (message.includes('admin') || message.includes('administrative')) {
                    return 'I can assist with administrative tasks like updating your profile, managing your schedule, and handling patient documentation. What administrative task do you need help with?';
                } else if (message.includes('treatment') || message.includes('therapy')) {
                    return 'I can provide general information about treatment options and therapies, but remember that treatment decisions should always be based on individual patient assessment and current medical evidence.';
                } else if (message.includes('hello') || message.includes('hi')) {
                    return 'Hello Doctor! I\'m BioXNet, your medical AI assistant. How can I assist you today? I\'m here to help with patient care, medical queries, and administrative tasks.';
                } else if (message.includes('ehr') || message.includes('electronic')) {
                    return 'Electronic Health Records are accessible through the EHR section. You can view patient medical history, update records, and manage prescriptions there.';
                } else {
                    return 'Thank you for your query. I\'m BioXNet, specialized in patient management and medical protocols. How can I help you further?';
                }
            }
            
            // Hybrid model responses for doctors
            function getHybridResponse(message) {
                if (message.includes('patient') || message.includes('manage')) {
                    return 'As Hybrid AI, I can provide advanced patient analytics, identify risk patterns, and suggest personalized treatment approaches. I can also analyze patient data trends and recommend preventive interventions.';
                } else if (message.includes('appointment') || message.includes('schedule')) {
                    return 'I can provide intelligent scheduling optimization, predict patient no-shows, and suggest optimal time slots based on patient history and medical urgency. I can also help with resource allocation.';
                } else if (message.includes('guideline') || message.includes('protocol')) {
                    return 'I can access the latest medical research, cross-reference multiple guidelines, and provide evidence-based recommendations. I can also analyze conflicting guidelines and suggest the most current best practices.';
                } else if (message.includes('research') || message.includes('study')) {
                    return 'I can perform comprehensive literature reviews, analyze clinical trial data, and provide meta-analyses. I can also identify research gaps and suggest areas for further investigation.';
                } else if (message.includes('admin') || message.includes('administrative')) {
                    return 'I can optimize administrative workflows, analyze efficiency patterns, and suggest process improvements. I can also help with documentation automation and quality assurance.';
                } else if (message.includes('treatment') || message.includes('therapy')) {
                    return 'I can provide evidence-based treatment recommendations, analyze treatment outcomes, and suggest personalized therapeutic approaches based on patient characteristics and medical history.';
                } else if (message.includes('hello') || message.includes('hi')) {
                    return 'Hello Doctor! I\'m Hybrid, your advanced medical AI with comprehensive knowledge and analytical capabilities. I can provide deeper insights, pattern analysis, and evidence-based recommendations. How can I help you today?';
                } else if (message.includes('ehr') || message.includes('electronic')) {
                    return 'I can provide advanced EHR analytics, identify data quality issues, and suggest improvements. I can also analyze patient outcomes and suggest evidence-based interventions.';
                } else {
                    return 'Thank you for your query. As Hybrid AI, I can provide advanced medical insights, comprehensive research analysis, and sophisticated patient care recommendations. How can I help you further?';
                }
            }
            
            // Handle form submission
            chatForm.on('submit', function(e) {
                e.preventDefault();
                const message = messageInput.val().trim();
                
                if (message) {
                    addMessage(message, true);
                    messageInput.val('');
                    simulateAIResponse(message);
                }
            });
            
            // Handle quick action buttons
            $('.quick-action').on('click', function() {
                const action = $(this).data('action');
                let message = '';
                
                switch(action) {
                    case 'patients':
                        message = 'Help me with patient management';
                        break;
                    case 'appointments':
                        message = 'Show my appointment schedule';
                        break;
                    case 'guidelines':
                        message = 'What are the latest medical guidelines?';
                        break;
                    case 'research':
                        message = 'Help me find medical research';
                        break;
                    case 'admin':
                        message = 'I need administrative assistance';
                        break;
                    case 'protocols':
                        message = 'Show me treatment protocols';
                        break;
                }
                
                if (message) {
                    addMessage(message, true);
                    simulateAIResponse(message);
                }
            });
            
            // Enter key to send message
            messageInput.on('keypress', function(e) {
                if (e.which === 13) {
                    chatForm.submit();
                }
            });
            
            // Focus on input when page loads
            messageInput.focus();
            
            // Initialize model info
            updateModelInfo();
        });
    </script>
</body>
</html>
