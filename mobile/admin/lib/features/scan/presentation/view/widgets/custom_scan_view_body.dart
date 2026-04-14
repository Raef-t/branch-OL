// ignore_for_file: use_build_context_synchronously
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:mobile_scanner/mobile_scanner.dart';
import '/core/components/failure_state_component.dart';
import '/core/components/text_medium14_component.dart';
import '/core/components/text_medium16_component.dart';
import '/core/components/text_medium18_component.dart';
import '/core/helpers/pop_go_router_helper.dart';
import '/core/styles/colors_style.dart';
import '/features/scan/presentation/managers/cubits/scan_qr_cubit.dart';
import '/features/scan/presentation/managers/cubits/scan_qr_state.dart';

class CustomScanViewBody extends StatefulWidget {
  const CustomScanViewBody({super.key});

  @override
  State<CustomScanViewBody> createState() => _CustomScanViewBodyState();
}

class _CustomScanViewBodyState extends State<CustomScanViewBody> {
  late MobileScannerController mobileScannerController;
  @override
  void initState() {
    mobileScannerController = MobileScannerController();
    super.initState();
  }

  @override
  void dispose() {
    mobileScannerController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return BlocListener<ScanQrCubit, ScanQrState>(
      listener: (context, state) async {
        if (state is ScanQrSuccessState) {
          mobileScannerController.stop(); //stop camera on working
          await showDialog(
            context: context,
            barrierDismissible: false,
            builder: (_) => CupertinoAlertDialog(
              title: const TextMedium18Component(text: 'بيانات الطالب'),
              content: Column(
                children: [
                  TextMedium16Component(text: state.student.fullName ?? ''),
                  TextMedium16Component(
                    text: 'Branch ID: ${state.student.branchId}',
                  ),
                  TextMedium16Component(
                    text:
                        'Institute Branch ID: ${state.student.instituteBranchId}',
                  ),
                ],
              ),
              actions: [
                CupertinoDialogAction(
                  child: const TextMedium14Component(
                    text: 'حسناً',
                    color: ColorsStyle.mediumGreenColor,
                  ),
                  onPressed: () {
                    popGoRouterHelper(context: context);
                    mobileScannerController.start();
                    //make the camera is working
                  },
                ),
              ],
            ),
          );
        }
        if (state is ScanQrFailureState) {
          mobileScannerController.stop(); //stop camera on working
          await showDialog(
            context: context,
            barrierDismissible: false,
            builder: (_) => CupertinoAlertDialog(
              title: const TextMedium18Component(text: 'خطأ'),
              content: FailureStateComponent(errorText: state.errorMessage),
              actions: [
                CupertinoDialogAction(
                  child: const TextMedium14Component(
                    text: 'إغلاق',
                    color: ColorsStyle.redColor,
                  ),
                  onPressed: () {
                    popGoRouterHelper(context: context);
                    mobileScannerController.start();
                    //make the camera is working
                  },
                ),
              ],
            ),
          );
        }
      },
      child: MobileScanner(
        controller: mobileScannerController,
        onDetect: (capture) {
          final Barcode barcode = capture.barcodes.first;
          final String? code = barcode.rawValue;
          if (code != null) {
            context.read<ScanQrCubit>().scanQr(qrContent: code);
          }
        },
      ),
    );
  }
}
