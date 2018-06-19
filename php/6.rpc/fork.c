#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>

#define NUM 10

int main(int argc, const char *argv[])
{
	pid_t pid_arr[NUM];
	int i;
	char num_str[100];
	for (i = 1; i <= NUM; i++) {
		pid_arr[i] = fork();
		if (pid_arr[i]==0) {//child
			snprintf(num_str, 100, "%d",i);
			execl("/usr/bin/php", "", "rpc_client.php", num_str, NULL);//第二个参数无所谓 
			exit(0);
		}else if(pid_arr[i]>0){//parent

		}else{
			printf("error\n");
			exit(1);
		}
	}

	for (i = 0; i < NUM; i++) {
		wait(NULL);
	}

	return 0;
}
