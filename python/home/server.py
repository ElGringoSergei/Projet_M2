import logging
import socket
from atexit import register
from datetime import datetime
from logging.handlers import TimedRotatingFileHandler
from os import chmod, makedirs, path, remove, stat
from pickle import loads, dumps
from subprocess import run
from threading import Thread

# import sqlalchemy
import apscheduler.events
# from apscheduler.jobstores.sqlalchemy import SQLAlchemyJobStore
from apscheduler.schedulers.background import BackgroundScheduler

# Logs directory
log_dir = 'logs'

# If logs directory does not exist, it's created
if not path.exists(log_dir):
    makedirs(log_dir)

# Configure logger
date_format = "%Y-%m-%d"  # Date in log file name
log_filename = path.join(log_dir, f'scheduler_{datetime.now().strftime(date_format)}.log')  # Configure log file name
handler = TimedRotatingFileHandler(log_filename, when="midnight", interval=1,
                                   encoding='utf-8')  # Setup rotating date for log file name
handler.setLevel(logging.INFO)  # Setup INFO as log level
formatter = logging.Formatter('%(asctime)s.%(msecs)d - %(levelname)s - %(message)s',
                              datefmt='%Y-%m-%d %H:%M:%S')  # Format each line of log file with date, time and milliseconds
handler.setFormatter(formatter)  # Set the formatter

#  Create logger with handler
logger = logging.getLogger()  # Create logger instance
logger.setLevel(logging.INFO)  # Add log level
logger.addHandler(handler)  # Add handler to logger instance


class JobSchedulerServer:
    """
        Process the jobs requests and launch the associated scripts
        """

    # TODO: Set database then fill the URL
    def __init__(self, host, port):
        self.host = host
        self.port = port
        self.scheduler = BackgroundScheduler()
        # jobstores={'default': SQLAlchemyJobStore(url='mysql://user:password@localhost:3306/mydatabase')})
        self.nodes_ip = {
            "1": "10.30.193.16",
            "2": "10.30.193.17",
            "3": "10.30.193.18",
            "4": "10.30.193.19",
            "5": "10.30.193.20",
            "6": "10.30.193.21",
            "7": "10.30.193.22",
            "8": "10.30.193.23"
        }

    def shutdown_hook(self):
        """Called when the program exits"""
        logging.info("Server shutting down...")
        self.scheduler.shutdown()

    def shutdown(self):
        """Called when the server is shutting down"""
        logging.info("Server shutdown")
        self.scheduler.shutdown()
        logging.shutdown()

    #  TODO : get script information
    @staticmethod
    def create_script(nodes, script_path, path_to_binary_file):
        """
        Create the bash script that will be executed later
        :param nodes: IP addresses of the nodes that will execute the binary file
        :param script_path: path to the directory where the script will be registered
        :param path_to_binary_file: path to the binary file that will be sent by the bash script
        :return:
        """
        try:
            file = open(script_path, 'w')
            data = "start\n"

            for node in nodes:
                data = data + str(node) + " "
            data = data + path_to_binary_file

            # Write the script
            file.write(data + "\nend")
            file.close()

            # Make script executable
            chmod(script_path, stat(script_path).st_mode | 0o111)
            logger.info(f"Script {script_path} created")
            return True
        except IOError:
            logger.error(f"Script {script_path} not created")
            return False

    @staticmethod
    def execute_script(script_path):
        """
        Run the 'filename' bash script then delete it
        :param script_path: path to the directory where the script is registered
        :return:
        """
        # Run the script setup in the job
        if path.exists(script_path):
            run(script_path, shell=True)
            remove(script_path)
            logger.info(f"Script {script_path} executed")
        else:
            logger.error(f"Script {script_path} not found")

    def create_job(self, script_path, binary_path, run_time, nodes):
        """
        Create a job based on the parameters
        :param nodes: IP addresses of the nodes that will execute the binary file
        :param script_path: path to the directory where the script is registered
        :param binary_path: path to the binary file that will be sent by the bash script
        :param run_time: the time at which the script will run, as a Datetime
        :return:
        """
        # Create the script that will be run in the job
        cs = self.create_script(nodes, script_path, binary_path)

        if not cs:
            logger.error(f"Script {script_path} not created")
            return -1
        try:
            # Add the job to the scheduler
            job = self.scheduler.add_job(self.execute_script, args=[script_path], next_run_time=run_time,
                                         misfire_grace_time=60)
            logger.info(f"Job {job.id} created")
            return job.id
        except apscheduler.events.EVENT_JOB_ERROR:
            # If an error occur during the creation, we delete the script
            logger.error(f"Job was not created")
            remove(script_path)
            return -3

    def delete_job(self, script_path, job_id):
        """
        Delete a job based on its ID
        :param script_path: path to the directory where the script is registered
        :param job_id: the id of the job
        :return:
        """
        if self.scheduler.get_job(job_id) is not None:
            # Remove job from scheduler
            self.scheduler.remove_job(job_id)

            # Remove job script
            if path.exists(script_path):
                remove(script_path)
                logger.info(f"Script {script_path} from job {job_id} deleted")

            # logger.info(f"Job {job_id} deleted")
            return job_id
        else:
            logger.error(f"Job {job_id} doesn't exist, cannot delete")
            return -1

    def modify_execution_time(self, job_id, new_execution_time):
        """
        Change the execution time of a job
        :param job_id: the id of the job
        :param new_execution_time: the time at which the script will run, as a Datetime
        :return:
        """
        if self.scheduler.get_job(job_id) is not None:
            self.scheduler.modify_job(job_id, next_run_time=new_execution_time)
            logger.info(f"Job {job_id} rescheduled. New execution time is {new_execution_time}")
            return job_id
        else:
            logger.error(f"Job {job_id} doesn't exist, cannot reschedule")
            return -1

    # TODO: Implement logic to modify the binary file in the script
    def modify_binary_file(self, script_path, job_id, new_binary_data):
        """
        Modify the binary that will be sent by the script
        :param script_path: path to the directory where the script is registered
        :param job_id: the id of the job
        :param new_binary_data: the path to the new binary file
        :return:
        """
        if self.scheduler.get_job(job_id) is not None:
            # Write the new binary file information in the script
            try:
                with open(script_path, 'a') as script_file:
                    script_file.write("\n" + new_binary_data)
                logger.info(f"Job {job_id} modified: binary file changed")
                return job_id
            except FileNotFoundError as e:
                logger.error(f"Script {script_path} not found: {e}")
                return -2
        else:
            logger.error(f"Job {job_id} doesn't exist, cannot modify binary file")
            return -1

    def handle_request(self, request):
        """
        Handle the requests received through the socket connection
        :param request: the request to handle
        :return: response
        """
        # Unpack the request using pickle
        command, *args = loads(request)

        # Execute the corresponding function
        if command == 'add_job':
            result = self.create_job(*args)
        elif command == 'delete_job':
            result = self.delete_job(*args)
        elif command == 'modify_execution_time':
            result = self.modify_execution_time(*args)
        elif command == 'modify_binary_file':
            result = self.modify_binary_file(*args)
        else:
            logger.error("Invalid request received")
            result = dumps("Invalid request received")

        if result == -1:
            return dumps("Job does not exist")
        elif result == -2:
            return dumps("Script file does not exist")
        elif result == -3:
            return dumps("Job creation error")
        else:
            return dumps(result)

    def handle_connection(self, conn, addr):
        with conn:
            try:
                logger.info(f"Connection established by {addr}")

                # Receive the request from the client
                request = conn.recv(1024)
                if not request:
                    # Connection closed by the client
                    logger.info(f"Connection closed by {addr}")
                    return

                logger.info("Request received. Processing")

                # Handle the request and send its response
                response = self.handle_request(request)
                conn.sendall(response)

            except ConnectionResetError:
                # Connection was reset by the client
                logger.warning(f"Connection reset by {addr}")

    def start(self):
        """
        Start the server
        :return:
        """
        logger.info("Server starting...")

        # Start the scheduler from APScheduler
        self.scheduler.start()

        # Register a function to be called when exiting
        register(self.shutdown_hook)

        # Prepare the futures connections
        try:
            with (socket.socket(socket.AF_INET, socket.SOCK_STREAM) as server_socket):
                logger.info("Server socket initializing...")
                server_socket.bind((self.host, self.port))
                server_socket.listen()
                logger.info("Server socket initialized. Waiting for connection")
                logger.info("Server started")
                try:
                    while True:
                        conn, addr = server_socket.accept()
                        Thread(target=self.handle_connection, args=(conn, addr)).start()
                except ConnectionError as e:
                    logger.error(f"Connection error: {e}")
                except (KeyboardInterrupt, SystemExit):
                    self.shutdown()
                except socket.error as e:
                    logger.error(f"Socket error: {e}")
                    self.shutdown()
        except OSError as e:
            logger.error(f"OSError: {e}")
            logging.shutdown()
        except KeyboardInterrupt:
            logger.error("Server shutdown by KeyboardInterrupt")
            logging.shutdown()
        except Exception as e:
            logger.error(f"Unexpected error: {e}")
            logging.shutdown()


if __name__ == "__main__":
    # Start the server
    logger.info("Server initializing...")
    server = JobSchedulerServer(host='127.0.0.1', port=12345)  # TODO: Set host and port
    logger.info("Server initialized")
    server.start()

#     Requests received:
#     "add_job", "path/to/script.sh", "path/to/file.bin", "YYYY-MM-DD hh:mm:ss", ["1.1.1.1","1.1.1.2"]
#     "modify_execution_time", "id", "YYYY-MM-DD hh:mm:ss"
#     "modify_binary_file", "id", "path/to/script.sh", "path/to/file.bin"
#     "delete_job", "id", "path_to_script.sh"

#  If the time of execution is immediate, set time to next minute (current minute + 1)
