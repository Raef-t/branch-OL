import 'package:flutter/cupertino.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:qr_flutter/qr_flutter.dart';
import '/core/components/circle_loading_state_component.dart';
import '/core/components/failure_state_component.dart';
import '/core/paddings/padding_with_child/symmetric_padding_with_child.dart';
import '/features/q_r/presentation/managers/cubits/door_session_cubit.dart';
import '/features/q_r/presentation/managers/cubits/door_session_state.dart';

class CustomQRViewBody extends StatelessWidget {
  const CustomQRViewBody({super.key});

  @override
  Widget build(BuildContext context) {
    return BlocBuilder<DoorSessionCubit, DoorSessionState>(
      builder: (context, state) {
        if (state is DoorSessionSuccessState) {
          final data = state.doorSessionModel.token ?? '';
          return SymmetricPaddingWithChild.horizontal30(
            context: context,
            child: Center(
              child: QrImageView(data: data, version: QrVersions.auto),
            ),
          );
        } else if (state is DoorSessionFailureState) {
          return FailureStateComponent(errorText: state.errorMessage);
        } else {
          return const CircleLoadingStateComponent();
        }
      },
    );
  }
}
