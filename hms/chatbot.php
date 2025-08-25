<?php
session_start();
include('include/config.php');
include('include/checklogin.php');
check_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Patient | Chatbot</title>
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="vendor/themify-icons/themify-icons.min.css">
    <link href="vendor/animate.css/animate.min.css" rel="stylesheet" media="screen">
    <link href="vendor/perfect-scrollbar/perfect-scrollbar.min.css" rel="stylesheet" media="screen">
    <link href="vendor/switchery/switchery.min.css" rel="stylesheet" media="screen">
    <link href="vendor/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css" rel="stylesheet" media="screen">
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="screen">
    <link href="vendor/bootstrap-datepicker/bootstrap-datepicker3.standalone.min.css" rel="stylesheet" media="screen">
    <link href="vendor/bootstrap-timepicker/bootstrap-timepicker.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/plugins.css">
    <link rel="stylesheet" href="assets/css/themes/theme-1.css" id="skin_color" />
    
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
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
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
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
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
            border-left-color: #0056b3;
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
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
            outline: none;
        }
        .chat-input button {
            border-radius: 25px;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,123,255,0.3);
        }
        .chat-input button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,123,255,0.4);
        }
        .typing-indicator {
            display: none;
            color: #6c757d;
            font-style: italic;
            margin: 0 20px 20px 20px;
            padding: 10px 15px;
            background: #f8f9fa;
            border-radius: 15px;
            border-left: 3px solid #007bff;
        }
        .model-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #007bff;
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
            border: 2px solid #007bff;
            padding: 10px 15px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .model-selector select:focus {
            border-color: #0056b3;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
            outline: none;
        }
        .model-selector label {
            color: #495057;
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
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
        .quick-action {
            margin: 5px;
            border-radius: 20px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 2px solid #007bff;
            background: white;
            color: #007bff;
        }
        .quick-action:hover {
            background: #007bff;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,123,255,0.3);
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
            color: #007bff;
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
            color: #007bff;
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
                                    <i class="ti-comments"></i> AI Chatbot Assistant
                                </h1>
                            </div>
                            <ol class="breadcrumb">
                                <li>
                                    <span>Patient</span>
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
                                        <h4 class="panel-title">Medical Assistant Chatbot</h4>
                                    </div>
                                    <div class="panel-body">
                                        <div class="chat-container">
                                            <div class="chat-header">
                                                <h4><i class="fa fa-robot"></i> Medical AI Assistant</h4>
                                                <small>Ask me anything about your health, appointments, or medical records</small>
                                            </div>
                                            
                                            <div class="chat-messages" id="chatMessages">
                                                <div class="message bot">
                                                    <div class="message-content">
                                                        Hello! I'm your medical AI assistant. How can I help you today? You can ask me about:
                                                        <br>• Your appointments
                                                        <br>• Medical records
                                                        <br>• General health questions
                                                        <br>• Hospital services
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="typing-indicator" id="typingIndicator">
                                                AI is typing...
                                            </div>
                                            
                                            <div class="chat-input">
                                                <form id="chatForm" class="form-inline">
                                                    <div class="form-group" style="width: 85%;">
                                                        <input type="text" class="form-control" id="messageInput" 
                                                               placeholder="Type your message here..." style="width: 100%;">
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fa fa-paper-plane"></i> Send
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        
                                        <div class="row mt-3">
                                            <div class="col-md-12">
                                                <h5>Quick Actions:</h5>
                                                <button class="btn btn-outline-primary btn-sm quick-action" data-action="appointment">Check Appointments</button>
                                                <button class="btn btn-outline-primary btn-sm quick-action" data-action="records">View Medical Records</button>
                                                <button class="btn btn-outline-primary btn-sm quick-action" data-action="booking">Book Appointment</button>
                                                <button class="btn btn-outline-primary btn-sm quick-action" data-action="help">General Help</button>
                                            </div>
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
                                                        <strong>BioXNet:</strong> Specialized in medical queries, appointments, and health records
                                                    </div>
                                                    <div id="hybrid-info" class="model-details" style="display: none;">
                                                        <strong>Hybrid:</strong> Advanced AI with comprehensive medical knowledge and research capabilities
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
                const message = `Switched to ${modelName} model. How can I assist you?`;
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
            
            // BioXNet model responses
            function getBioXNetResponse(message) {
                if (message.includes('appointment') || message.includes('schedule')) {
                    return 'I can help you with appointments! You can check your appointment history or book a new appointment. Would you like me to guide you to the appointment booking page?';
                } else if (message.includes('medical') || message.includes('record') || message.includes('ehr')) {
                    return 'Your medical records are available in the Electronic Health Records section. You can view your medical history, prescriptions, and other health information there.';
                } else if (message.includes('health') || message.includes('symptom')) {
                    return 'I can provide general health information, but please remember that I\'m not a substitute for professional medical advice. For specific symptoms or concerns, please consult with your doctor.';
                } else if (message.includes('book') || message.includes('new')) {
                    return 'To book a new appointment, you can go to the "Book Appointment" section. I can help you understand the process or answer any questions you might have.';
                } else if (message.includes('hello') || message.includes('hi')) {
                    return 'Hello! I\'m BioXNet, your medical AI assistant. How can I assist you today? I\'m here to help with your medical queries, appointments, and general health information.';
                } else {
                    return 'Thank you for your message. I\'m BioXNet, specialized in medical appointments, records, and general health information. How can I assist you further?';
                }
            }
            
            // Hybrid model responses
            function getHybridResponse(message) {
                if (message.includes('appointment') || message.includes('schedule')) {
                    return 'As Hybrid AI, I can provide advanced appointment assistance with predictive scheduling, conflict resolution, and optimization suggestions. I can also analyze your appointment patterns to recommend better time slots.';
                } else if (message.includes('medical') || message.includes('record') || message.includes('ehr')) {
                    return 'I can provide comprehensive analysis of your medical records, identify patterns, and offer insights. I can also cross-reference your data with medical research and suggest preventive measures.';
                } else if (message.includes('health') || message.includes('symptom')) {
                    return 'I can analyze symptoms using advanced medical databases, provide detailed health insights, and suggest relevant medical literature. However, always consult healthcare professionals for diagnosis.';
                } else if (message.includes('book') || message.includes('new')) {
                    return 'I can help you find the optimal appointment time based on your schedule, doctor availability, and medical urgency. I can also suggest alternative doctors if needed.';
                } else if (message.includes('hello') || message.includes('hi')) {
                    return 'Hello! I\'m Hybrid, your advanced medical AI with comprehensive knowledge and analytical capabilities. I can provide deeper insights and more sophisticated medical assistance. How can I help you today?';
                } else {
                    return 'Thank you for your message. As Hybrid AI, I can provide advanced medical insights, pattern analysis, and comprehensive health information. How can I assist you further?';
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
                    case 'appointment':
                        message = 'Check my appointments';
                        break;
                    case 'records':
                        message = 'Show my medical records';
                        break;
                    case 'booking':
                        message = 'Help me book an appointment';
                        break;
                    case 'help':
                        message = 'What can you help me with?';
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
