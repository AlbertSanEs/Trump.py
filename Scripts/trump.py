import ast
import subprocess
import shlex
import datetime
import time
import socket
import ssl
import math
import os

def getNetworking():
    new_env = dict(os.environ)
    new_env['LC_ALL'] = 'C'

    ifconfig = shlex.split("ifconfig -a")
    grep = shlex.split("grep -e 'Link encap' -e 'inet'")
    loGrep = shlex.split("grep -v -e 'lo' -e '127.0.0.1' -e 'inet6'")
    awk = shlex.split("awk '{print $1','$2','$4','$5}'")

    sIf = subprocess.Popen(ifconfig, stdout=subprocess.PIPE, env=new_env)
    sGrep = subprocess.Popen(grep,stdout=subprocess.PIPE, stdin=sIf.stdout)
    losGrep = subprocess.Popen(loGrep, stdout=subprocess.PIPE, stdin=sGrep.stdout)
    sAwk = str(subprocess.check_output(awk, stdin=losGrep.stdout)).split("b'")[1].split("\\n'")[0].split("\\n")

    devices = []
    for idx, i in enumerate(sAwk):
        i = i.split(" ")
        if "lo" == i[0]:
            sAwk.pop(idx)
            if "inet" == sAwk[idx][0]:
                sAwk.pop(idx)
        else:
            if i[0] != "inet":
                devices.append(i[0] + " " + i[-1])
            else:
                devices[len(devices)-1] += "\n" + i[1].split("addr:")[1] + " " + i[2].split("Mask:")[1]

    jDevices = {}
    for i in devices:
        i = i.split("\n")

        if len(i) > 1:
            device = i[0].split("Device:")[0].split(" ")[0]
            mac = i[0].split("Device:")[0].split(" ")[1]
            ip = i[1].split("IP:")[0].split(" ")[0]
            mask = i[1].split("Mask:")[0].split(" ")[1]

            jDevices[device] = {}
            jDevices[device]["MAC"] = mac
            jDevices[device]["IP"] = ip
            jDevices[device]["Netmask"] = mask

        elif len(i) == 1:
            split = i[0].split("Device:")[0].split(" ")
            # Split 0 equals Network card device
            # Split 1 equals MAC Address
            jDevices[str(split[0])] = {}
            jDevices[str(split[0])]["MAC"] = split[1]

    return jDevices

def getCPUstats():
    cpu = {}
    grep = shlex.split("grep 'cpu ' /proc/stat")
    awk = shlex.split("awk '{usage=($2+$4)*100/($2+$4+$5)} END {print usage}'")
    sGrep = subprocess.Popen(grep, stdout=subprocess.PIPE)
    sAwk = str(subprocess.check_output(awk, stdin=sGrep.stdout)).split("b'")[1].split("\\n'")[0]
    cpu["usage"] = sAwk

    sensors = shlex.split("sensors")
    grepC = shlex.split("grep 'Core'")
    awkT = shlex.split("awk '{print $3$6$9}'")
    sSensors = subprocess.Popen(sensors, stdout=subprocess.PIPE)
    pGrep = subprocess.Popen(grepC, stdin=sSensors.stdout,stdout=subprocess.PIPE)
    sAwkT = str(subprocess.check_output(awkT, stdin=pGrep.stdout)).split("b'")[1].split("\\n'")[0].split("\\n")

    cpu["temps"] = []
    for idx, i in enumerate(sAwkT):
        cpu["temps"].append({})
        cpu["temps"][idx]["temp"] = i.split("\\")[0].split("+")[1]
        cpu["temps"][idx]["high"] = i.split("\\")[2].split("+")[1]
        cpu["temps"][idx]["crit"] = i.split("\\")[4].split("+")[1]

    return cpu

def getSpace():
    df = shlex.split("df -mh")
    grep = shlex.split("grep ^/.*")
    tr = shlex.split("tr -s ' '")

    sDf = subprocess.Popen(df, stdout=subprocess.PIPE)
    sGrep = subprocess.Popen(grep, stdin=sDf.stdout, stdout=subprocess.PIPE)
    sTr = str(subprocess.check_output(tr, stdin=sGrep.stdout)).split("b'")[1].split("\\n'")[0].split("\\n")

    space = {}
    for i in sTr:
        i = i.split(" ")
        i[0] = i[0].split("/dev/")[1]
        space[str(i[0])] = {}
        space[str(i[0])]["mount"] = i[5]
        space[str(i[0])]["size"] = i[1][:-1] + " " + i[1][-1:]
        space[str(i[0])]["free"] = i[3][:-1] + " " + i[3][-1:]
        space[str(i[0])]["used"] = i[2][:-1] + " " + i[2][-1:]
        space[str(i[0])]["usedx100"] = i[4]

    return space

def getRam():
    free = shlex.split("free -m")
    grep = shlex.split("grep 'Mem.*:'")
    awk = shlex.split("awk '{print $2/1024,$3/1024}'")
    ram = {}

    sFree = subprocess.Popen(free, stdout=subprocess.PIPE)
    sGrep = subprocess.Popen(grep, stdin=sFree.stdout, stdout=subprocess.PIPE)
    sAwk = str(subprocess.check_output(awk, stdin=sGrep.stdout)).split("b'")[1].split("\\n'")[0].split(" ")

    ram["total"] = round(float(sAwk[0]), 1)
    ram["used"] = round(float(sAwk[1]), 1)

    return ram

def getName():
    hostname = shlex.split("hostname")
    sHostname = str(subprocess.Popen(hostname, stdout=subprocess.PIPE).communicate()[0]).split("b'")[1].split("\\n")[0]

    return sHostname

def getKernelArch():
    uname = shlex.split("uname -iro")
    sUname = str(subprocess.Popen(uname, stdout=subprocess.PIPE).communicate()[0]).split("b'")[1].split("\\n'")[0].split(" ")
    kernelArch = {}
    kernelArch["kernel"] = str(sUname[0])
    kernelArch["arch"] = str(sUname[1])
    kernelArch["os"] = str(sUname[2])

    return kernelArch

def getPkgInfo():
    dpkg = shlex.split("dpkg --get-selections")
    grep = shlex.split("grep -v deinstall")
    list = []
    pkgInfo = {}
    sDpkg = subprocess.Popen(dpkg, stdout=subprocess.PIPE)
    sGrep = str(subprocess.check_output(grep, stdin=sDpkg.stdout)).split("b'")[1].split("\\n'")[0].split("\\n")

    for i in sGrep:
        i = i.split("\\t")
        vDpkg = shlex.split("dpkg -s " + i[0])
        vGrep = shlex.split("grep '^Version'")
        svGrep = False
        #svDpkg = subprocess.Popen(vDpkg, stdout=subprocess.PIPE)
        #svGrep = str(subprocess.check_output(vGrep, stdin=svDpkg.stdout)).split("b'")[1].split("\\n'")[0].split(" ")[1]

        if svGrep:
            list.append(i[0] + "(" + svGrep + ")")
        else:
            list.append(i[0])

    pkgInfo["list"] = list
    pkgInfo["number"] = len(list)

    return pkgInfo

def getUptime():
    uptime = shlex.split("uptime -s")
    sUptime = str(subprocess.Popen(uptime, stdout=subprocess.PIPE).communicate()[0]).split("b'")[1].split("\\n'")[0]

    return sUptime

def getHddTemps():
    temps = {}
    ls = shlex.split("ls '/dev/'")
    grep = shlex.split("grep ^sd.$")
    sLs = subprocess.Popen(ls,stdout=subprocess.PIPE)
    sGrep = str(subprocess.Popen(grep, stdin=sLs.stdout, stdout=subprocess.PIPE).communicate()[0]).split("b'")[1].split("\\n'")[0].split("\\n")

    for i in sGrep:
        hddtemp = shlex.split("hddtemp /dev/" + i + " --unit=C")
        sHddtemp = str(subprocess.Popen(hddtemp,stdout=subprocess.PIPE).communicate()[0]).split(" ")[-1].split("\\")[0]
        if str(sHddtemp) != "b''":
            temps[str(i)] = str(sHddtemp)
        else:
            temps[str(i)] = "null"

    return temps

def getUsers():
    users = []
    w = shlex.split("w")
    tr = shlex.split("tr -s ' '")
    sW = subprocess.Popen(w, stdout=subprocess.PIPE)
    sTr = str(subprocess.Popen(tr, stdin=sW.stdout,stdout=subprocess.PIPE).communicate()[0]).split("b'")[1].split("\\n'")[0].split("\\n")
    sTr.pop(0)
    sTr.pop(0)
    for i in sTr:
        i = i.split(" ")
        while len(i) > 8:
            i[7] = i[7] + " " + i[8]
            i.pop(8)
        users.append(i)

    return users

userId = subprocess.Popen(shlex.split("id"), stdout=subprocess.PIPE)
userId = subprocess.check_output(shlex.split("awk '{print $1}'"), stdin=userId.stdout)
userId = int(str(userId).split("b'")[1].split("=")[1].split("(")[0])

if userId == 0:
    timing = 29
    while 1:
        time.sleep(1)
        timing += 1
        if timing == 30:
            report = {}
            now = datetime.datetime.now()
            report["timestamp"] = str(now.year) + "-"

            if now.month < 10: report["timestamp"] += "0" + str(now.month) + "-"
            else: report["timestamp"] += str(now.month) + "-"

            if now.day < 10: report["timestamp"] += "0" + str(now.day) + " "
            else: report["timestamp"] += str(now.day) + " "

            if now.hour < 10: report["timestamp"] += "0" + str(now.hour) + ":"
            else: report["timestamp"] += str(now.hour) + ":"

            if now.minute < 10: report["timestamp"] += "0" + str(now.minute) + ":"
            else: report["timestamp"] += str(now.minute) + ":"

            if now.second < 10: report["timestamp"] += "0" + str(now.second)
            else: report["timestamp"] += str(now.second)

            report["hostname"] = getName()
            report["network"] = getNetworking()
            report["uptime"] = getUptime()
            report["users"] = getUsers()
            report["ram"] = getRam()
            report["cpu"] = getCPUstats()
            report["space"] = getSpace()
            report["disktemps"] = getHddTemps()
            report["kernel"] = getKernelArch()

            numPart = 4
            pkg = str(getPkgInfo())
            quart = math.ceil(len(pkg) / numPart)

            pkgPart = []

            pkgPart.append(pkg[:quart])
            pkgPart.append(pkg[quart:quart * 2])
            pkgPart.append(pkg[quart * 2:quart * 3])
            pkgPart.append(pkg[quart * 3:])

            try:
                context = ssl.SSLContext(ssl.PROTOCOL_TLSv1)
                clientsocket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
                conn = context.wrap_socket(clientsocket)
                conn.connect(('172.16.3.24', 8089))
                conn.send(str(report).encode())
                for i in pkgPart:
                    conn.send(i.encode())

                commands = conn.recv(1024*1024).decode()
                if len(commands) > 0:
                    commands = ast.literal_eval(commands)

                    for i in commands:
                        subprocess.Popen(shlex.split(i));
            except ConnectionRefusedError:
                print("Can't reach the server. It's the server on?")

            finally:
                conn.close()

            timing = 0
else:
    print("Necesitas ser root para ejecutar este script con todas sus funcionalidades.")
    timing = 29
    while 1:
        time.sleep(1)
        timing += 1
        if timing == 30:
            report = {}
            now = datetime.datetime.now()
            report["timestamp"] = str(now.year) + "-"

            if now.month < 10:
                report["timestamp"] += "0" + str(now.month) + "-"
            else:
                report["timestamp"] += str(now.month) + "-"

            if now.day < 10:
                report["timestamp"] += "0" + str(now.day) + " "
            else:
                report["timestamp"] += str(now.day) + " "

            if now.hour < 10:
                report["timestamp"] += "0" + str(now.hour) + ":"
            else:
                report["timestamp"] += str(now.hour) + ":"

            if now.minute < 10:
                report["timestamp"] += "0" + str(now.minute) + ":"
            else:
                report["timestamp"] += str(now.minute) + ":"

            if now.second < 10:
                report["timestamp"] += "0" + str(now.second)
            else:
                report["timestamp"] += str(now.second)

            report["hostname"] = getName()
            report["network"] = getNetworking()
            report["uptime"] = getUptime()
            report["users"] = getUsers()
            report["ram"] = getRam()
            report["cpu"] = getCPUstats()
            report["space"] = getSpace()
            #report["disktemps"] = getHddTemps()
            report["kernel"] = getKernelArch()

            numPart = 4
            pkg = str(getPkgInfo())
            quart = math.ceil(len(pkg) / numPart)

            pkgPart = []

            pkgPart.append(pkg[:quart])
            pkgPart.append(pkg[quart:quart * 2])
            pkgPart.append(pkg[quart * 2:quart * 3])
            pkgPart.append(pkg[quart * 3:])

            try:
                context = ssl.SSLContext(ssl.PROTOCOL_TLSv1)
                clientsocket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
                conn = context.wrap_socket(clientsocket)
                conn.connect(('172.16.3.24', 8089))
                conn.send(str(report).encode())
                for i in pkgPart:
                    conn.send(i.encode())

                commands = conn.recv(1024 * 1024).decode()
                if len(commands) > 0:
                    commands = ast.literal_eval(commands)

                    for i in commands:
                        subprocess.Popen(shlex.split(i));
            except ConnectionRefusedError:
                print("Can't reach the server. It's the server on?")

            finally:
                conn.close()

            timing = 0